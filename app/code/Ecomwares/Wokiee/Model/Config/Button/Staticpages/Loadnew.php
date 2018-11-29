<?php /**/
namespace Ecomwares\Wokiee\Model\Config\Button\Staticpages;
class Loadnew
{
    protected $reader;
    private $parser;
    protected $pageColFactory;
    protected $pageRepositoryInterface;
    protected $pageFactory;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Framework\Module\Dir\Reader $reader, \Magento\Framework\Xml\Parser $parser, \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $pageColFactory, \Magento\Cms\Model\PageFactory $pageFactory, \Magento\Cms\Api\PageRepositoryInterface $pageRepositoryInterface, array $data = [])
    {
        $this->reader = $reader;
        $this->parser = $parser;
        $this->pageFactory = $pageFactory;
        $this->pageColFactory = $pageColFactory;
        $this->pageRepositoryInterface = $pageRepositoryInterface;
    }

    public function importStaticPages()
    {
        $filePath = $this->reader->getModuleDir('etc', 'Ecomwares_Wokiee') . '/import/cms_pages.xml';
        if (!is_readable($filePath)) {
            throw new \Exception(__("File is not readable. Please check permissions" . $filePath));
            return 0;
        } else {
            $parsedArray = $this->parser->load($filePath)->xmlToArray();
            foreach ($parsedArray['default']['pages']['page'] as $page) {
                $page['stores'] = [0];
                $check_page_exists = $this->pageColFactory->create()->addFieldToFilter('identifier', $page['identifier']);
                if (!count($check_page_exists)) $this->pageFactory->create()->setData($page)->save();
            }
            return 1;
        }
    }
}