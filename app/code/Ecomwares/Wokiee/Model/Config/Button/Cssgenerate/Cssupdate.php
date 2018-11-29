<?php /**/

namespace Ecomwares\Wokiee\Model\Config\Button\Cssgenerate;
class Cssupdate
{
    protected $reader;
    private   $parser;
    protected $blockColFactory;
    protected $configInterface;
    protected $objectManagerInterface;
    protected $storeManager;
    protected $coreRegistry;
    protected $layoutInterface;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Module\Dir\Reader $reader,
        \Magento\Framework\Xml\Parser $parser,
        \Magento\Cms\Model\ResourceModel\Block\CollectionFactory $blockColFactory,
        \Magento\Cms\Model\BlockFactory $blockFactory,
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configInterface,
        \Magento\Framework\ObjectManagerInterface $objectManagerInterface,
        \Magento\Framework\Registry $registry,
        array $data = []
    ){
        $this->reader = $reader;
        $this->parser = $parser;
        $this->blockFactory = $blockFactory;
        $this->blockColFactory = $blockColFactory;
        $this->configInterface = $configInterface;
        $this->objectManagerInterface = $objectManagerInterface;
        $this->registry = $registry;
        $this->storeInterface = $this->objectManagerInterface->get('Ecomwares\Wokiee\Helper\Data')->getStoreInterface();
        $this->layoutInterface = $this->objectManagerInterface->get('Ecomwares\Wokiee\Helper\Data')->getLayoutInterface();
    }
    
    public function refreshCssFiles()
    {
        $websites = $this->storeInterface->getWebsites(false, false);
        foreach ($websites as $id => $value)
        {
            $website = $this->storeInterface->getWebsite($id);
            foreach($website->getStoreIds() as $storeId){
                $this->refreshStoreCss($storeId);
            }
        }
    }

    public function refreshStoreCss($storeId)
    {
        $store = $this->storeInterface->getStore($storeId);
        if(!$store->isActive())return;
        $this->registry->register('store_for_css', $store->getCode());
        $css_output = $this->layoutInterface->createBlock('Ecomwares\Wokiee\Block\Template')->setData('area','frontend')->setTemplate('css/css.phtml')->toHtml();
        $path_folder = BP.'/pub/media/wokiee/css/';
        $path_file=$path_folder.$store->getCode().'.css';
        if(!empty($css_output)) {
            try {
                if(!file_exists($path_folder)) @mkdir($path_folder, 0777);
                $file = @fopen($path_file,"w+");
                @fwrite($file,$css_output);
                @fclose($file);
                $this->registry->unregister('store_for_css');
                return true;
            } catch (\Exception $e) {
                $e->getMessage();
            }
        }
        return false;
    }
}