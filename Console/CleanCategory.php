<?php
/**
 * @copyright ©2024 RdTest
 * @license   RdTest
 */

namespace RdTest\Cleanup\Console;
 
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Exception\LocalizedException;
 
class CleanCategory extends Command
{


    protected $registry;
    protected $categoryFactory;
    protected $storeManager;

    public function __construct(
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry
    ) {
        $this->registry = $registry;
        $this->categoryFactory = $categoryFactory;
        $this->storeManager = $storeManager;
        parent::__construct();
    }

 
    protected function configure() {
       $this->setName('categories:cleanup');
       $this->setDescription('Remove all the categories');
      
       parent::configure();
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output) {

        $categories = $this->removeCategories($output);
        $output->writeln('<info>Deleted ' . $categories . ' categories</info>');   
        $exitCode = 0;

        try {
        // throw new LocalizedException(__('An error occurred.'));
        } catch (LocalizedException $e) {
        $output->writeln(sprintf('<error>%s</error>',$e->getMessage()));
        $exitCode = 1;
        }
        return $exitCode;
    }

    /**
     * Remove categories
     *
     * @return void
     */
    protected function removeCategories(OutputInterface $output) {
        $categories = $this->categoryFactory->create()->getCollection();

        $ids = [\Magento\Catalog\Model\Category::TREE_ROOT_ID];
        foreach ($this->storeManager->getGroups() as $store) {
            $ids[] = $store->getRootCategoryId();
        }      
 
        $this->registry->register("isSecureArea", true);
        $i=0;
        foreach ($categories as $category) {
            if (!in_array($category->getId(),$ids)) {
                if ($output->isVerbose()) {
                    $output->writeln('Deleted: ' . $category->getName());
                }
                $category->delete();
                $i++;
            }
        }
        return $i;       
 
    }

}