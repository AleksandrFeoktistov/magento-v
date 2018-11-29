<?php /* Colors */
namespace Ecomwares\Wokiee\Block\System\Config;
class Color extends \Magento\Config\Block\System\Config\Form\Field
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = $element->getElementHtml();
        $html .= '<script type="text/javascript">var picker = document.getElementById("'. $element->getHtmlId() .'"); picker.className = picker.className + " jscolor {required:false,hash:true}"; </script>';
        return $html;
    }
}