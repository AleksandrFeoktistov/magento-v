<?php /**/
namespace Ecomwares\Wokiee\Model\Config\Button\Skins;
class Load
{
    protected $reader;
    private $parser;
    protected $blockColFactory;
    protected $configFactory;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Framework\Module\Dir\Reader $reader, \Magento\Framework\Xml\Parser $parser, \Magento\Cms\Model\ResourceModel\Block\CollectionFactory $blockColFactory, \Magento\Cms\Model\BlockFactory $blockFactory, \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configFactory, array $data = [])
    {
        $this->reader = $reader;
        $this->parser = $parser;
        $this->blockFactory = $blockFactory;
        $this->blockColFactory = $blockColFactory;
        $this->configFactory = $configFactory;
    }

    public function importSkin($skin_xml, $store)
    {
        $scope = "default";
        $scope_id = 0;
        if ($store)
        {
            if($store > 0)
            {
                $scope = "stores";
                $scope_id = $store;
            }

        }

        $filePath = $this->reader->getModuleDir('etc', 'Ecomwares_Wokiee') . '/import/configsets/'.$skin_xml.'.xml';
        if (!is_readable($filePath)) {
            throw new \Exception(__($skin_xml. ".xml File is not readable. Please check permissions" . $filePath));
            return 0;
        } else {
            $parsedArray = $this->parser->load($filePath)->xmlToArray();

            foreach($parsedArray['config']['sections'] as $section => $section_value){
                foreach($section_value as $group => $group_value){
                    foreach($group_value as $field => $val){
                        $this->configFactory->saveConfig($section.'/'.$group.'/'.$field,$val,$scope,$scope_id);
                    }
                }
            }

            return 1;
        }

    }
}