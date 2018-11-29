<?php /**/
namespace Ecomwares\Wokiee\Model\Config\Button\Install;
class Theme
{
    protected $reader;
    private $parser;
    protected $blockColFactory;
    protected $configInterface;
    protected $objectManagerInterface;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Framework\Module\Dir\Reader $reader, \Magento\Framework\Xml\Parser $parser, \Magento\Cms\Model\ResourceModel\Block\CollectionFactory $blockColFactory, \Magento\Cms\Model\BlockFactory $blockFactory, \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configInterface, \Magento\Framework\ObjectManagerInterface $objectManagerInterface, array $data = [])
    {
        $this->reader = $reader;
        $this->parser = $parser;
        $this->blockFactory = $blockFactory;
        $this->blockColFactory = $blockColFactory;
        $this->configInterface = $configInterface;
        $this->objectManagerInterface = $objectManagerInterface;
    }

    public function installTheme()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('theme');
        $sql = "Select * FROM " . $tableName . " WHERE theme_path = 'Ecomwares/wokiee'";
        $result = $connection->fetchAll($sql);
        if (count($result)) {
            $theme_id = $result[0]['theme_id'];
            $scope = 'default';
            $scopeId = 0;
            $this->configInterface->saveConfig('design/theme/theme_id', $theme_id, $scope, $scopeId);
            $this->configInterface->saveConfig('web/default/cms_home_page', 'wokiee_home_page_default', $scope, $scopeId);
            $this->configInterface->saveConfig('web/default/cms_no_route', 'wokiee_404', $scope, $scopeId);
            $this->configInterface->saveConfig('cms/wysiwyg_enabled', 'hidden', $scope, $scopeId);
            return 1;
        } else return 0;
    }
}