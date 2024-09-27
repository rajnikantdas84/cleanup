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
use Magento\Framework\App\ResourceConnection;
 
class CleanProduct extends Command
{
    protected $registry;
    protected $state;
    private $resourceconnection;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\State $state,
        ResourceConnection $resourceconnection
    ) 
    {
        $this->registry = $registry;
        $this->state = $state;
        $this->resourceconnection = $resourceconnection;
        parent::__construct();
    }

    /**
    * Set the command
    */
    protected function configure() {
        $this->setName('products:cleanup');
        $this->setDescription('Remove all the products related records');

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

        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
        $this->registry->register("isSecureArea", true);

        $connection = $this->resourceconnection->getConnection();

        $message = 0;
        $exitCode = 0;
        $sql = "SET FOREIGN_KEY_CHECKS=0";
        

        try {
            $connection->query($sql);
            $output->writeln('<info>'.$sql.';</info>');
        } catch (\LocalizedException $e) {
            $output->writeln(sprintf('<error>%s</error>',$e->getMessage()));
            $exitCode = 1;
        }               

        $productsTables = array(
            'cataloginventory_stock_item',
            'cataloginventory_stock_status',
            'cataloginventory_stock_status_idx',
            'cataloginventory_stock_status_tmp',
            'catalog_category_product',
            'catalog_category_product_index',
            'catalog_category_product_index_tmp',
            'catalog_compare_item',
            'catalog_product_bundle_option',
            'catalog_product_bundle_option_value',
            'catalog_product_bundle_price_index',
            'catalog_product_bundle_selection',
            'catalog_product_bundle_selection_price',
            'catalog_product_bundle_stock_index',
            'catalog_product_entity',
            'catalog_product_entity_datetime',
            'catalog_product_entity_decimal',
            'catalog_product_entity_gallery',
            'catalog_product_entity_int',
            'catalog_product_entity_media_gallery',
            'catalog_product_entity_media_gallery_value',
            'catalog_product_entity_media_gallery_value_to_entity',
            'catalog_product_entity_media_gallery_value_video',
            'catalog_product_entity_text',
            'catalog_product_entity_tier_price',
            'catalog_product_entity_varchar',
            'catalog_product_index_eav',
            'catalog_product_index_eav_decimal',
            'catalog_product_index_eav_decimal_idx',
            'catalog_product_index_eav_decimal_tmp',
            'catalog_product_index_eav_idx',
            'catalog_product_index_eav_tmp',
            'catalog_product_index_price',
            'catalog_product_index_price_bundle_idx',
            'catalog_product_index_price_bundle_opt_idx',
            'catalog_product_index_price_bundle_opt_tmp',
            'catalog_product_index_price_bundle_sel_idx',
            'catalog_product_index_price_bundle_sel_tmp',
            'catalog_product_index_price_bundle_tmp',
            'catalog_product_index_price_cfg_opt_agr_idx',
            'catalog_product_index_price_cfg_opt_agr_tmp',
            'catalog_product_index_price_cfg_opt_idx',
            'catalog_product_index_price_cfg_opt_tmp',
            'catalog_product_index_price_downlod_idx',
            'catalog_product_index_price_downlod_tmp',
            'catalog_product_index_price_final_idx',
            'catalog_product_index_price_final_tmp',
            'catalog_product_index_price_idx',
            'catalog_product_index_price_opt_agr_idx',
            'catalog_product_index_price_opt_agr_tmp',
            'catalog_product_index_price_opt_idx',
            'catalog_product_index_price_opt_tmp',
            'catalog_product_index_price_tmp',
            'catalog_product_index_tier_price',
            'catalog_product_index_website',
            'catalog_product_link',
            'catalog_product_link_attribute_decimal',
            'catalog_product_link_attribute_int',
            'catalog_product_link_attribute_varchar',
            'catalog_product_option',
            'catalog_product_option_price',
            'catalog_product_option_title',
            'catalog_product_option_type_price',
            'catalog_product_option_type_title',
            'catalog_product_option_type_value',
            'catalog_product_relation',
            'catalog_product_super_attribute',
            'catalog_product_super_attribute_label',
            'catalog_product_super_link',
            'catalog_product_website',
            'catalog_url_rewrite_product_category',
            'downloadable_link',
            'downloadable_link_price',
            'downloadable_link_purchased',
            'downloadable_link_purchased_item',
            'downloadable_link_title',
            'downloadable_sample',
            'downloadable_sample_title',
            'product_alert_price',
            'product_alert_stock',
            'report_compared_product_index',
            'report_viewed_product_aggregated_daily',
            'report_viewed_product_aggregated_monthly',
            'report_viewed_product_aggregated_yearly',
            'report_viewed_product_index',
            'inventory_source_item',
            'review',
            'review_detail',
            'review_entity_summary',
            'review_store',
            'sequence_product',
            'wishlist',
            'wishlist_item',
            'wishlist_item_option',
            'requisition_list',
            'requisition_list_item'           
        );

        foreach($productsTables as $table) {

            try {
                $connection->truncateTable($table);
                $altersql = "ALTER TABLE $table AUTO_INCREMENT=1";
                $connection->query($altersql);
                $output->writeln('<info>TRUNCATE TABLE ' . $table .';</info>');
                $output->writeln('<info>' . $altersql .';</info>');

            } catch (\LocalizedException $e) {
                $output->writeln(sprintf('<error>%s</error>',$e->getMessage()));
                $exitCode = 1;
            }            
        }

        $sql2 = "SET FOREIGN_KEY_CHECKS=1";
        

        try {
            $connection->query($sql2);
            $output->writeln('<info>'.$sql2.';</info>');
        } catch (\LocalizedException $e) {
            $output->writeln(sprintf('<error>%s</error>',$e->getMessage()));
            $exitCode = 1;
        }

        $sql3 = "DELETE FROM url_rewrite WHERE entity_type='product'";

        try {
            $connection->query($sql3);
            $output->writeln('<info>'.$sql3.';</info>');
        } catch (\LocalizedException $e) {
            $output->writeln(sprintf('<error>%s</error>',$e->getMessage()));
            $exitCode = 1;
        }        

        return $exitCode;
    }
}