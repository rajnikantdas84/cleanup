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
 
class CleanOrder extends Command
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
        $this->setName('orders:cleanup');
        $this->setDescription('Remove all the order related records');

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

        $orderTables = array(
            'gift_message',
            'quote',
            'quote_address',
            'quote_address_item',
            'quote_id_mask',
            'quote_item',
            'quote_item_option',
            'quote_payment',
            'quote_shipping_rate',
            'reporting_orders',
            'sales_bestsellers_aggregated_daily',
            'sales_bestsellers_aggregated_monthly',
            'sales_bestsellers_aggregated_yearly',
            'sales_creditmemo',
            'sales_creditmemo_comment',
            'sales_creditmemo_grid',
            'sales_creditmemo_item',
            'sales_invoice',
            'sales_invoiced_aggregated',
            'sales_invoiced_aggregated_order',
            'sales_invoice_comment',
            'sales_invoice_grid',
            'sales_invoice_item',
            'sales_order',
            'sales_order_address',
            'sales_order_aggregated_created',
            'sales_order_aggregated_updated',
            'sales_order_grid',
            'sales_order_item',
            'sales_order_payment',
            'sales_order_status_history',
            'sales_order_tax',
            'sales_order_tax_item',
            'sales_payment_transaction',
            'sales_refunded_aggregated',
            'sales_refunded_aggregated_order',
            'sales_shipment',
            'sales_shipment_comment',
            'sales_shipment_grid',
            'sales_shipment_item',
            'sales_shipment_track',
            'sales_shipping_aggregated',
            'sales_shipping_aggregated_order',
            'tax_order_aggregated_created',
            'tax_order_aggregated_updated',
            'magento_rma',
            'magento_rma_grid',
            'magento_rma_item_entity',
            'magento_rma_item_entity_int',
            'magento_rma_status_history',
            'magento_rma_shipping_label',
            'company_order_entity',                    
            'sequence_order_0',
            'sequence_order_1',
            'sequence_order_2',
            'sequence_invoice_0',
            'sequence_invoice_1',
            'sequence_invoice_2',
            'sequence_creditmemo_0',
            'sequence_creditmemo_1',
            'sequence_creditmemo_2',
            'sequence_shipment_0',
            'sequence_shipment_1',
            'sequence_shipment_2',
            'sequence_rma_item_0',
            'sequence_rma_item_1',
            'sequence_rma_item_2',
            'negotiable_quote',
            'negotiable_quote_comment',
            'negotiable_quote_comment_attachment',
            'negotiable_quote_grid',
            'negotiable_quote_history',
            'negotiable_quote_item',
            'negotiable_quote_item_note',
            'negotiable_quote_purged_content',
            'purchase_order',
            'purchase_order_applied_rule',
            'purchase_order_applied_rule_approver',
            'purchase_order_approved_by',
            'purchase_order_comment',
            'purchase_order_log',
            'purchase_order_rule',
            'purchase_order_rule_applies_to',
            'purchase_order_rule_approver',
            'sequence_purchase_order_0',
            'sequence_purchase_order_1',
            'sequence_purchase_order_2'                    
        );

        foreach($orderTables as $table) {

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


        return $exitCode;
    }
}