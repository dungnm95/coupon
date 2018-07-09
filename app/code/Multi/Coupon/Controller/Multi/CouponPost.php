<?php
/**
 * Created by PhpStorm.
 * User: dzung
 * Date: 04/07/2018
 * Time: 14:26
 */

namespace Multi\Coupon\Controller\Multi;

class CouponPost extends \Magento\Checkout\Controller\Cart
{
    /**
     * Sales quote repository
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * Coupon factory
     *
     * @var \Magento\SalesRule\Model\CouponFactory
     */
    protected $couponFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\SalesRule\Model\CouponFactory $couponFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\SalesRule\Model\CouponFactory $couponFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    )
    {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
        $this->couponFactory = $couponFactory;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Initialize coupon
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
//        \Zend_Debug::dump($this->getRequest()->getParams()); die('ckdvn');
        $couponCode = $this->getRequest()->getParam('remove') == 1
            ? ''
            : trim($this->getRequest()->getParam('coupon_code'));

        $cartQuote = $this->cart->getQuote();
        $oldCouponCode = $cartQuote->getCouponCode();
        $coupon_code_input = trim($this->getRequest()->getParam('coupon_code'));

        try {
            $check_coupon = true;

            if ($this->getRequest()->getParam('remove') == 1) {
//            return $this->_goBack();
                $array_old_coupon = explode(',', $oldCouponCode);
                $coupon_code_remove = $this->getRequest()->getParam('remove_coupon');
                $array_coupon_after_remove = [];
                foreach ($array_old_coupon as $code) {
                    if ($code != $coupon_code_remove) {
                        $array_coupon_after_remove[] = $code;
                    }
                }
                $couponCode = implode(',', $array_coupon_after_remove);

            } else {
//                \Zend_Debug::dump($oldCouponCode . '/' . $couponCode); die('dd');
                if ($oldCouponCode) {
                    $coupon = $this->couponFactory->create();
                    $coupon->load($couponCode, 'code');
                    if (!$coupon->getId()) {
                        $check_coupon = false;
                        $couponCode = $oldCouponCode;
                    } else {
                        if ($oldCouponCode == $couponCode) {
                            $couponCode = $oldCouponCode;
                        } else {
                            $couponCode = $oldCouponCode . ',' . $couponCode;
                        }
                    }

                }
            }
            $codeLength = strlen($couponCode);
            $isCodeLengthValid = $codeLength && $codeLength <= \Magento\Checkout\Helper\Cart::COUPON_CODE_MAX_LENGTH;

//            \Zend_Debug::dump($couponCode); die('ckdvn');

            $itemsCount = $cartQuote->getItemsCount();
            if ($itemsCount) {
                $cartQuote->getShippingAddress()->setCollectShippingRates(true);
                $cartQuote->setCouponCode($isCodeLengthValid ? $couponCode : '')->collectTotals();
                $this->quoteRepository->save($cartQuote);
            }

            if ($codeLength) {
                $escaper = $this->_objectManager->get(\Magento\Framework\Escaper::class);

                $array_coupon = explode(',', $couponCode);
                foreach ($array_coupon as $code) {

                }

                if (!$itemsCount) {
                    if ($isCodeLengthValid && $check_coupon) {
                        $this->_checkoutSession->getQuote()->setCouponCode($couponCode)->save();
                        $this->messageManager->addSuccess(
                            __(
                                'You used coupon code "%1".',
                                $escaper->escapeHtml($coupon_code_input)
                            )
                        );
                    } else {
                        $this->messageManager->addError(
                            __(
                                'The coupon code "%1" is not valid.',
                                $escaper->escapeHtml($coupon_code_input)
                            )
                        );
                    }
                } else {
                    if ($isCodeLengthValid && $check_coupon && $couponCode == $cartQuote->getCouponCode()) {
                        $this->messageManager->addSuccess(
                            __(
                                'You used coupon code "%1".',
                                $escaper->escapeHtml($coupon_code_input)
                            )
                        );
                    } else {
                        $this->messageManager->addError(
                            __(
                                'The coupon code "%1" is not valid.',
                                $escaper->escapeHtml($coupon_code_input)
                            )
                        );
                    }
                }
            } else {
                $this->messageManager->addSuccess(__('You canceled the coupon code.'));
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We cannot apply the coupon code.'));
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
        }

        return $this->_goBack();
    }
}
