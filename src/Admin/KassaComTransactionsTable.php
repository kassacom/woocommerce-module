<?php


namespace KassaCom\WCPlugin\Admin;


class KassaComTransactionsTable extends \WP_List_Table {

	public function prepare_items() {
		$pageSize    = 25;
		$page        = $this->get_pagenum();
		$this->items = $this->getTransactions( $page, $pageSize );
		$totalItems  = $this->getTotal();

		$columns               = $this->get_columns();
		$hidden                = [];
		$sortable              = [];
		$this->_column_headers = [ $columns, $hidden, $sortable ];

		$this->set_pagination_args( [
			'total_items' => $totalItems,
			'per_page'    => $pageSize,
			'total_pages' => ceil( $totalItems / $pageSize ),
		] );
	}

	public function get_columns() {
		return [
			'ID'             => __( 'Id заказа', 'kassa-com-checkout' ),
			'transaction_id' => __( 'Id платежа', 'kassa-com-checkout' ),
			'post_status'    => __( 'Статус', 'kassa-com-checkout' ),
			'post_title'     => __( 'Описание заказа', 'kassa-com-checkout' ),
		];
	}

	public function column_default( $item, $column_name ) {
		$statusList = wc_get_order_statuses();

		switch ( $column_name ) {
			case 'ID':
			case 'post_title':
			case 'transaction_id':
				return $item[ $column_name ];
				break;
			case 'post_status':
				$status = $item[ $column_name ];

				return isset( $statusList[ $status ] ) ? $statusList[ $status ] : '--------';
				break;
			default:
				return print_r( $item, true );
		}
	}

	/**
	 * @param int $page
	 * @param int $perPage
	 *
	 * @return array|null|object
	 */
	protected function getTransactions( $page, $perPage ) {
		global $wpdb;

		$query = "
			SELECT p.ID, p.post_status, p.post_title, trans.meta_value AS transaction_id FROM {$wpdb->posts} p 
			INNER JOIN {$wpdb->postmeta} method 
				ON p.ID = method.post_id
			INNER JOIN {$wpdb->postmeta} trans 
				ON p.ID = trans.post_id
			WHERE trans.meta_key = '_transaction_id'
			AND trans.meta_value <> '' 
			AND method.meta_key = '_payment_method' 
			AND method.meta_value = 'kassa-com'
			ORDER BY p.post_date DESC
			LIMIT %d
			OFFSET %d";

		$offset = ( $page - 1 ) * $perPage;
		$sql    = $wpdb->prepare( $query, $perPage, $offset );

		return $wpdb->get_results( $sql, ARRAY_A );
	}

	/**
	 * @return int
	 */
	protected function getTotal() {
		global $wpdb;

		$query = "
			SELECT COUNT(p.ID) FROM {$wpdb->posts} p 
			INNER JOIN {$wpdb->postmeta} method 
				ON p.ID = method.post_id
			INNER JOIN {$wpdb->postmeta} trans 
				ON p.ID = trans.post_id
			WHERE trans.meta_key = '_transaction_id'
			AND trans.meta_value <> '' 
			AND method.meta_key = '_payment_method' 
			AND method.meta_value = 'kassa-com'";

		return (int) $wpdb->get_var( $query );
	}
}
