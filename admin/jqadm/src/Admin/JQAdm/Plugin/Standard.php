<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2021
 * @package Admin
 * @subpackage JQAdm
 */


namespace Aimeos\Admin\JQAdm\Plugin;

sprintf( 'settings' ); // for translation
sprintf( 'plugin' ); // for translation


/**
 * Default implementation of plugin JQAdm client.
 *
 * @package Admin
 * @subpackage JQAdm
 */
class Standard
	extends \Aimeos\Admin\JQAdm\Common\Admin\Factory\Base
	implements \Aimeos\Admin\JQAdm\Common\Admin\Factory\Iface
{
	/**
	 * Adds the required data used in the template
	 *
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @return \Aimeos\MW\View\Iface View object with assigned parameters
	 */
	public function addData( \Aimeos\MW\View\Iface $view ) : \Aimeos\MW\View\Iface
	{
		$ds = DIRECTORY_SEPARATOR;

		$view->itemDecorators = $this->getClassNames( 'MShop' . $ds . 'Plugin' . $ds . 'Provider' . $ds . 'Decorator' );
		$view->itemProviders = [
			'order' => $this->getClassNames( 'MShop' . $ds . 'Plugin' . $ds . 'Provider' . $ds . 'Order' )
		];

		$view->itemSubparts = $this->getSubClientNames();
		$view->itemTypes = $this->getTypeItems();

		return $view;
	}


	/**
	 * Copies a resource
	 *
	 * @return string|null HTML output
	 */
	public function copy() : ?string
	{
		$view = $this->getObject()->addData( $this->getView() );

		try
		{
			if( ( $id = $view->param( 'id' ) ) === null ) {
				throw new \Aimeos\Admin\JQAdm\Exception( sprintf( 'Required parameter "%1$s" is missing', 'id' ) );
			}

			$manager = \Aimeos\MShop::create( $this->getContext(), 'plugin' );

			$view->item = $manager->get( $id );
			$view->itemData = $this->toArray( $view->item, true );
			$view->itemAttributes = $this->getConfigAttributes( $view->item );
			$view->itemBody = parent::copy();
		}
		catch( \Exception $e )
		{
			$this->report( $e, 'copy' );
		}

		return $this->render( $view );
	}


	/**
	 * Creates a new resource
	 *
	 * @return string|null HTML output
	 */
	public function create() : ?string
	{
		$view = $this->getObject()->addData( $this->getView() );

		try
		{
			$data = $view->param( 'item', [] );

			if( !isset( $view->item ) ) {
				$view->item = \Aimeos\MShop::create( $this->getContext(), 'plugin' )->create();
			}

			$data['plugin.siteid'] = $view->item->getSiteId();

			$view->itemData = array_replace_recursive( $this->toArray( $view->item ), $data );
			$view->itemBody = parent::create();
		}
		catch( \Exception $e )
		{
			$this->report( $e, 'create' );
		}

		return $this->render( $view );
	}


	/**
	 * Deletes a resource
	 *
	 * @return string|null HTML output
	 */
	public function delete() : ?string
	{
		$view = $this->getView();

		$manager = \Aimeos\MShop::create( $this->getContext(), 'plugin' );
		$manager->begin();

		try
		{
			if( ( $ids = $view->param( 'id' ) ) === null ) {
				throw new \Aimeos\Admin\JQAdm\Exception( sprintf( 'Required parameter "%1$s" is missing', 'id' ) );
			}

			$search = $manager->filter()->slice( 0, count( (array) $ids ) );
			$search->setConditions( $search->compare( '==', 'plugin.id', $ids ) );
			$items = $manager->search( $search );

			foreach( $items as $item )
			{
				$view->item = $item;
				parent::delete();
			}

			$manager->delete( $items->toArray() );
			$manager->commit();

			return $this->redirect( 'plugin', 'search', null, 'delete' );
		}
		catch( \Exception $e )
		{
			$manager->rollback();
			$this->report( $e, 'delete' );
		}

		return $this->search();
	}


	/**
	 * Returns a single resource
	 *
	 * @return string|null HTML output
	 */
	public function get() : ?string
	{
		$view = $this->getObject()->addData( $this->getView() );

		try
		{
			if( ( $id = $view->param( 'id' ) ) === null ) {
				throw new \Aimeos\Admin\JQAdm\Exception( sprintf( 'Required parameter "%1$s" is missing', 'id' ) );
			}

			$manager = \Aimeos\MShop::create( $this->getContext(), 'plugin' );

			$view->item = $manager->get( $id );
			$view->itemData = $this->toArray( $view->item );
			$view->itemAttributes = $this->getConfigAttributes( $view->item );
			$view->itemBody = parent::get();
		}
		catch( \Exception $e )
		{
			$this->report( $e, 'get' );
		}

		return $this->render( $view );
	}


	/**
	 * Saves the data
	 *
	 * @return string|null HTML output
	 */
	public function save() : ?string
	{
		$view = $this->getView();

		$manager = \Aimeos\MShop::create( $this->getContext(), 'plugin' );
		$manager->begin();

		try
		{
			$item = $this->fromArray( $view->param( 'item', [] ) );
			$view->item = $item->getId() ? $item : $manager->save( $item );
			$view->itemBody = parent::save();

			$manager->save( clone $view->item );
			$manager->commit();

			return $this->redirect( 'plugin', $view->param( 'next' ), $view->item->getId(), 'save' );
		}
		catch( \Exception $e )
		{
			$manager->rollback();
			$this->report( $e, 'save' );
		}

		return $this->create();
	}


	/**
	 * Returns a list of resource according to the conditions
	 *
	 * @return string|null HTML output
	 */
	public function search() : ?string
	{
		$view = $this->getView();

		try
		{
			$total = 0;
			$params = $this->storeSearchParams( $view->param(), 'plugin' );
			$manager = \Aimeos\MShop::create( $this->getContext(), 'plugin' );

			$search = $manager->filter();
			$search->setSortations( [$search->sort( '+', 'plugin.type' ), $search->sort( '+', 'plugin.position' )] );
			$search = $this->initCriteria( $search, $params );

			$view->items = $manager->search( $search, [], $total );
			$view->filterAttributes = $manager->getSearchAttributes( true );
			$view->filterOperators = $search->getOperators();
			$view->itemTypes = $this->getTypeItems();
			$view->itemBody = parent::search();
			$view->total = $total;
		}
		catch( \Exception $e )
		{
			$this->report( $e, 'search' );
		}

		/** admin/jqadm/plugin/template-list
		 * Relative path to the HTML body template for the plugin list.
		 *
		 * The template file contains the HTML code and processing instructions
		 * to generate the result shown in the body of the frontend. The
		 * configuration string is the path to the template file relative
		 * to the templates directory (usually in admin/jqadm/templates).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "default" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "default"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating the HTML code
		 * @since 2016.04
		 * @category Developer
		 */
		$tplconf = 'admin/jqadm/plugin/template-list';
		$default = 'plugin/list-standard';

		return $view->render( $view->config( $tplconf, $default ) );
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return \Aimeos\Admin\JQAdm\Iface Sub-client object
	 */
	public function getSubClient( string $type, string $name = null ) : \Aimeos\Admin\JQAdm\Iface
	{
		/** admin/jqadm/plugin/decorators/excludes
		 * Excludes decorators added by the "common" option from the plugin JQAdm client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "client/jqadm/common/decorators/default" before they are wrapped
		 * around the JQAdm client.
		 *
		 *  admin/jqadm/plugin/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\Admin\JQAdm\Common\Decorator\*") added via
		 * "client/jqadm/common/decorators/default" to the JQAdm client.
		 *
		 * @param array List of decorator names
		 * @since 2017.10
		 * @category Developer
		 * @see admin/jqadm/common/decorators/default
		 * @see admin/jqadm/plugin/decorators/global
		 * @see admin/jqadm/plugin/decorators/local
		 */

		/** admin/jqadm/plugin/decorators/global
		 * Adds a list of globally available decorators only to the plugin JQAdm client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\Admin\JQAdm\Common\Decorator\*") around the JQAdm client.
		 *
		 *  admin/jqadm/plugin/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\Admin\JQAdm\Common\Decorator\Decorator1" only to the JQAdm client.
		 *
		 * @param array List of decorator names
		 * @since 2017.10
		 * @category Developer
		 * @see admin/jqadm/common/decorators/default
		 * @see admin/jqadm/plugin/decorators/excludes
		 * @see admin/jqadm/plugin/decorators/local
		 */

		/** admin/jqadm/plugin/decorators/local
		 * Adds a list of local decorators only to the plugin JQAdm client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\Admin\JQAdm\Plugin\Decorator\*") around the JQAdm client.
		 *
		 *  admin/jqadm/plugin/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\Admin\JQAdm\Plugin\Decorator\Decorator2" only to the JQAdm client.
		 *
		 * @param array List of decorator names
		 * @since 2017.10
		 * @category Developer
		 * @see admin/jqadm/common/decorators/default
		 * @see admin/jqadm/plugin/decorators/excludes
		 * @see admin/jqadm/plugin/decorators/global
		 */
		return $this->createSubClient( 'plugin/' . $type, $name );
	}


	/**
	 * Returns the backend configuration attributes of the provider and decorators
	 *
	 * @param \Aimeos\MShop\Plugin\Item\Iface $item Plugin item incl. provider/decorator property
	 * @return \Aimeos\MW\Common\Critera\Attribute\Iface[] List of configuration attributes
	 */
	public function getConfigAttributes( \Aimeos\MShop\Plugin\Item\Iface $item ) : array
	{
		$manager = \Aimeos\MShop::create( $this->getContext(), 'plugin' );

		try {
			return $manager->getProvider( $item, $item->getType() )->getConfigBE();
		} catch( \Aimeos\MShop\Exception $e ) {
			return [];
		}
	}


	/**
	 * Returns the list of sub-client names configured for the client.
	 *
	 * @return array List of JQAdm client names
	 */
	protected function getSubClientNames() : array
	{
		/** admin/jqadm/plugin/subparts
		 * List of JQAdm sub-clients rendered within the plugin section
		 *
		 * The output of the frontend is composed of the code generated by the JQAdm
		 * clients. Each JQAdm client can consist of serveral (or none) sub-clients
		 * that are responsible for rendering certain sub-parts of the output. The
		 * sub-clients can contain JQAdm clients themselves and therefore a
		 * hierarchical tree of JQAdm clients is composed. Each JQAdm client creates
		 * the output that is placed inside the container of its parent.
		 *
		 * At first, always the JQAdm code generated by the parent is printed, then
		 * the JQAdm code of its sub-clients. The order of the JQAdm sub-clients
		 * determines the order of the output of these sub-clients inside the parent
		 * container. If the configured list of clients is
		 *
		 *  array( "subclient1", "subclient2" )
		 *
		 * you can easily change the order of the output by reordering the subparts:
		 *
		 *  admin/jqadm/<clients>/subparts = array( "subclient1", "subclient2" )
		 *
		 * You can also remove one or more parts if they shouldn't be rendered:
		 *
		 *  admin/jqadm/<clients>/subparts = array( "subclient1" )
		 *
		 * As the clients only generates structural JQAdm, the layout defined via CSS
		 * should support adding, removing or reordering content by a fluid like
		 * design.
		 *
		 * @param array List of sub-client names
		 * @since 2017.10
		 * @category Developer
		 */
		return $this->getContext()->getConfig()->get( 'admin/jqadm/plugin/subparts', [] );
	}


	/**
	 * Returns the available plugin type items
	 *
	 * @return \Aimeos\Map List of IDs as keys and items implementing \Aimeos\MShop\Common\Type\Iface
	 */
	protected function getTypeItems() : \Aimeos\Map
	{
		$typeManager = \Aimeos\MShop::create( $this->getContext(), 'plugin/type' );

		$search = $typeManager->filter( true )->slice( 0, 10000 );
		$search->setSortations( [$search->sort( '+', 'plugin.type.position' )] );

		return $typeManager->search( $search );
	}


	/**
	 * Creates new and updates existing items using the data array
	 *
	 * @param array $data Data array
	 * @return \Aimeos\MShop\Plugin\Item\Iface New plugin item object
	 */
	protected function fromArray( array $data ) : \Aimeos\MShop\Plugin\Item\Iface
	{
		$conf = [];

		if( isset( $data['config']['key'] ) )
		{
			foreach( (array) $data['config']['key'] as $idx => $key )
			{
				if( trim( $key ) !== '' && isset( $data['config']['val'][$idx] ) )
				{
					if( ( $val = json_decode( $data['config']['val'][$idx] ) ) === null ) {
						$conf[$key] = $data['config']['val'][$idx];
					} else {
						$conf[$key] = $val;
					}
				}
			}
		}

		$manager = \Aimeos\MShop::create( $this->getContext(), 'plugin' );

		if( isset( $data['plugin.id'] ) && $data['plugin.id'] != '' ) {
			$item = $manager->get( $data['plugin.id'] );
		} else {
			$item = $manager->create();
		}

		$item->fromArray( $data, true );
		$item->setConfig( $conf );

		return $item;
	}


	/**
	 * Constructs the data array for the view from the given item
	 *
	 * @param \Aimeos\MShop\Plugin\Item\Iface $item Plugin item object
	 * @return string[] Multi-dimensional associative list of item data
	 */
	protected function toArray( \Aimeos\MShop\Plugin\Item\Iface $item, bool $copy = false ) : array
	{
		$config = $item->getConfig();
		$data = $item->toArray( true );
		$data['config'] = [];

		if( $copy === true )
		{
			$data['plugin.siteid'] = $this->getContext()->getLocale()->getSiteId();
			$data['plugin.id'] = '';
		}

		ksort( $config );

		foreach( $config as $key => $value )
		{
			$data['config']['key'][] = $key;
			$data['config']['val'][] = $value;
		}

		return $data;
	}


	/**
	 * Returns the rendered template including the view data
	 *
	 * @param \Aimeos\MW\View\Iface $view View object with data assigned
	 * @return string HTML output
	 */
	protected function render( \Aimeos\MW\View\Iface $view ) : string
	{
		/** admin/jqadm/plugin/template-item
		 * Relative path to the HTML body template for the plugin item.
		 *
		 * The template file contains the HTML code and processing instructions
		 * to generate the result shown in the body of the frontend. The
		 * configuration string is the path to the template file relative
		 * to the templates directory (usually in admin/jqadm/templates).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "default" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "default"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating the HTML code
		 * @since 2016.04
		 * @category Developer
		 */
		$tplconf = 'admin/jqadm/plugin/template-item';
		$default = 'plugin/item-standard';

		return $view->render( $view->config( $tplconf, $default ) );
	}
}
