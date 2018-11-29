<?php /**/
namespace Ecomwares\Wokiee\Block\System\Config\Button\Cssgenerate;
class Cssupdate extends \Magento\Config\Block\System\Config\Form\Field
{
    const BUTTON_TEMPLATE = 'system/config/button/cssgenerate/cssupdate.phtml';
    private $_lic;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Ecomwares\Wokiee\Helper\Lic $lic
    ) {
        $this->_lic = $lic;
        parent::__construct($context);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate(static::BUTTON_TEMPLATE);
        }
        return $this;
    }

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    public function getAjaxCheckUrl()
    {
        return $this->getUrl('addbutton/listdata');
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $licStatus = $this->_lic->getStatus();
        if ($licStatus) {
            $licNotice = '';
            $buttonClass = '';
        } else {
            //$buttonClass = 'disabled';
            //$licNotice = '<b style="color:#f00000;font-size:12px;line-height:1;">Activation is required.</b>';
            $licNotice = '';
            $buttonClass = '';
        }

        $this->addData(['id' => 'button_css_update', 'button_label' => __('Update CSS files'), 'button_class' => $buttonClass, 'onclick' => 'window.location.href=\'' . $this->_urlBuilder->getUrl('wokiee_admin/system_config_button_cssgenerate/cssupdate') . '\'', 'html_id' => $element->getHtmlId()]);
        return $this->_toHtml() . $licNotice;
    }
}