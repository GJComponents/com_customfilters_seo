<?php
/**
 *
 * Customfilters products view
 *
 * @package        customfilters
 * @author        Sakis Terz
 * @link        http://breakdesigns.net
 * @copyright    Copyright (c) 2012-2021 breakdesigns.net. All rights reserved.
 * @license        http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *                customfilters is free software. This version may have been modified
 *                pursuant to the GNU General Public License, and as distributed
 *                it includes or is derivative of works licensed under the GNU
 *                General Public License or other free or open source software
 *                licenses.
 */

// No direct access
defined('_JEXEC') or die();

require_once JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'cfview.php';

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;

class CustomfiltersViewProducts extends cfView
{
    /**
     *
     * @var string
     * @since 3.9
     */
    public $vm_version;

    /**
     *
     * @var int
     * @since 3.9
     */
    public $show_prices;

	/**
	 * Display function of the view
	 *
	 * @throws Exception
	 * @since 1.0.0
	 * @see   cfView::display()
	 */
    public function display($tpl = null)
    {
        $app = Factory::getApplication();
	    $paramsComponent = ComponentHelper::getParams('com_customfilters');


        $this->show_prices = (int)VmConfig::get('show_prices', 1);
        $this->addHelperPath(JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers');
        $this->load();
        $this->vm_version = VmConfig::getInstalledVersion();
        $this->showcategory = VmConfig::get('showCategory', 1);
        $this->showproducts = true;
        $this->showsearch = false;






        // get menu parameters
        $this->menuParams = cftools::getMenuparams();
        $vendorId = 1;
        $jinput = $app->input;
        $this->fallback = false;

        $categories = $jinput->get('virtuemart_category_id', array(), 'array');

	    /**
	     * Если выбрана только одна категория, а не ноль
	     * и в настройках компонента установленно - отображать вложенные категории
	     * Настройки компонента -> вкладка "Настройки SEO" -> "Отображение дочерних категорий"
	     * отобразить дочерние категории
	     *
	     * If there is only one category selected and is not zero, display children categories
	     */
        if (count($categories) == 1 && isset($categories[0]) && $categories[0] > 0 && $paramsComponent->get('on_show_children_category' , 1) ) {
            $this->categoryId = (int)$categories[0];
            if ($this->showcategory) {
                $category_haschildren = true;
            }
        }
        else {
            $this->categoryId = 0;
            $category_haschildren = false;
        }






        $categoryModel = VmModel::getModel('category');
		/**@var TableCategories $category */
        $category = $categoryModel->getCategory($this->categoryId);
        $catImgAmount = VmConfig::get('catimg_browse', 1) ? VmConfig::get('catimg_browse', 1) : 1;
        $categoryModel->addImages($category, $catImgAmount);
        $category->haschildren = $category_haschildren;

        if ($category_haschildren) {
            $category->children = $categoryModel->getChildCategoryList($vendorId, $this->categoryId,
                $categoryModel->getDefaultOrdering(), $categoryModel->_selectedOrderingDir);
            $categoryModel->addImages($category->children, $catImgAmount);
        }

        // triggers a content plugn for that category
        if (VmConfig::get('enable_content_plugin', 0) && method_exists('shopFunctionsF', 'triggerContentPlugin')) {
            shopFunctionsF::triggerContentPlugin($category, 'category', 'category_description');
        }


        $this->category = $category;
        $this->setVariablesFromParams();

	    // load basic libraries before any other script
	    $template = VmConfig::get('vmtemplate', 'default');
	    if (is_dir(JPATH_THEMES . DIRECTORY_SEPARATOR . $template)) {
		    $mainframe = Factory::getApplication();
		    $mainframe->set('setTemplate', $template);
	    }
	    $this->prepareDocument();

		// Если в настройках компонента com_customfilters - не отображать описание категории
	    if ( !$paramsComponent->get('on_description_vm_category' , 1 ) )
	    {
		    $this->category->category_description = null ;
	    }#END IF

		// on_description_vm_category

        /*
         * show base price variables
         */
        $user = Factory::getUser();
        $this->showBasePrice = ($user->authorise('core.administrator', 'com_virtuemart') || $user->authorise('core.manage',
                'com_virtuemart'));

        /*
         * get the products from the cf model
         */
        $this->productModel = VmModel::getModel('product');



        // rating
        $ratingModel = VmModel::getModel('ratings');
        $this->showRating = $ratingModel->showRating();
        $this->productModel->withRating = $this->showRating;


        $ids = $this->get('ProductListing');

        $this->products = $this->productModel->getProducts($ids);
        $this->productModel->addImages($this->products);
        /**
         * @var CustomfiltersModelProducts Object
         */
        $model = $this->getModel();

        if ($this->products) {
            $display_stock = VmConfig::get('display_stock', 1);
            $showCustoms = VmConfig::get('show_pcustoms', 1);

            if ($display_stock || $showCustoms) {

                if (!$showCustoms) {
                    foreach ($this->products as $i => $productItem) {
                        //assign stock to products
                        $this->products[$i]->stock = $this->productModel->getStockIndicator($productItem);
                    }
                } else {
                    //assign stock and custom fields to products
                    shopFunctionsF::sortLoadProductCustomsStockInd($this->products, $this->productModel);
                }
            }
        }

        $productsLayout = VmConfig::get('productsublayout', 'products');
        if (empty($productsLayout)) {
            $productsLayout = 'products';
        }
        $this->productsLayout = $productsLayout;
        // currency
        $currency = CurrencyDisplay::getInstance();
        $this->currency = $currency;

        /*
         * vm 3.0.18 and later saves the products in an assoc. array using as a key the product type
         * @todo Check that in later versions
         */
        $this->fallback = false;
        if (version_compare($this->vm_version, '4.0') > 0) {
            $products = $this->products;
            $this->products = [];
            $this->fallback = true;
            $this->products['0'] = $products;
        } // lower to 4.0
        else {
            $this->fallback = true;
            vmdebug('Fallback active');
        }

        $this->search = false;
        $this->searchcustom = '';
        $this->searchCustomValues = '';
        $this->add_product_link = '';





        /**
         * @var cfPagination Object my model's pagination
         */
        $this->vmPagination = $model->getPagination(true);



        $this->perRow = $this->menuParams->get('prod_per_row', 3);
        $this->orderByList = $this->get('OrderByList');



        parent::display($tpl);

        if (empty($this->products)) {
            echo '<span class="cf_results-msg">' . JText::_('COM_CUSTOMFILTERS_NO_PRODUCTS') . '</span>';
        }
    }

    /**
     * Prepares the document
     * @since 2.0.0
     */
    protected function prepareDocument()
    {
        $this->setCanonical();
        $this->setMeta();
        $this->setTitle();

        /*
         * Load scripts and styles
         */
        cftools::loadScriptsNstyles();

        // layout
        $this->_setPath('template',
            (JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtuemart' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'category' . DIRECTORY_SEPARATOR . 'tmpl'));
        $layout = $this->menuParams->get('cfresults_layout');
        $this->setLayout($layout);

        // load the virtuemart language files
        if (method_exists('VmConfig', 'loadJLang')) {
            VmConfig::loadJLang('com_virtuemart', true);
        } else {
            $language = Factory::getLanguage();
            $language->load('com_virtuemart');
        }
    }

    /**
     * Set the meta tags
     *
     * @return $this
     * @throws Exception
     * @since 2.8.8
     */
    protected function setTitle()
    {
        $titles = [];
        $delimiter = ' - ';
        $inputs = CfInput::getInputs(true);
        if (isset($inputs['q'])) {
            $titles [] = $inputs['q'];
        }
        if (isset($inputs['virtuemart_category_id'])) {
            $categoryIds = $inputs['virtuemart_category_id'];
            $categoryNames = CategoryHelper::getNames($categoryIds);
            $titles [] = implode(', ', $categoryNames);
        }

        if (isset($inputs['virtuemart_manufacturer_id'])) {
            $manufacturerIds = $inputs['virtuemart_manufacturer_id'];
            $manufacturerNames = ManufacturerHelper::getNames($manufacturerIds);
            $titles [] = implode(', ', $manufacturerNames);
        }


		//die(__FILE__ .' '. __LINE__ );


//        echo'<pre>';print_r( $titles );echo'</pre>'.__FILE__.' '.__LINE__ .'<br>';
//        die( __FILE__ .' ' . __LINE__);
        if (!empty($titles)) {
          //  $this->document->setTitle(implode($delimiter, $titles));
        }

        return $this;
    }

    /**
     * Set the meta tags
     *
     * @return $this
     * @throws Exception
     * @since 2.8.8
     */
    protected function setMeta()
    {
        $app = Factory::getApplication();

        return $this ;
        /*
         * Add meta data
         */
        if ($this->categoryId > 0 && !empty($this->category->metadesc)) {
            $this->document->setDescription($this->category->metadesc);
        } elseif ($this->menuParams->get('menu-meta_description')) {
            $this->document->setDescription( $this->menuParams->get('menu-meta_description'));
        }

        if ($this->categoryId > 0 && !empty($this->category->metakey)) {
            $this->document->setMetaData('keywords', $this->category->metakey);
        } elseif ($this->menuParams->get('menu-meta_keywords')) {
            $this->document->setMetadata('keywords', $this->menuParams->get('menu-meta_keywords'));
        }

        if ($this->categoryId > 0 && !empty($this->category->metarobot)) {
            $this->document->setMetaData('robots', $this->category->metarobot);
        } elseif ( $this->menuParams->get('robots') ) {
            $this->document->setMetadata('robots', $this->menuParams->get('robots'));
        }

        if ($app->get('MetaAuthor') && !empty($this->category->metaauthor)) {
            $this->document->setMetaData('author', $this->category->metaauthor);
        }
        return $this;
    }

    /**
     * Добавьте канонические URL-адреса в заголовок страниц.
     * Если есть другой канонический заменяет его на новый
     *
     * Add canonical urls to the head of the pages
     * If there is another canonical replaces it with a new one
     *
     * @since 2.2.0
     */
    protected function setCanonical()
    {

        $inputs = CfInput::getInputs();

        if (isset($inputs['virtuemart_category_id']) && count($inputs['virtuemart_category_id']) == 1
            || isset($inputs['virtuemart_manufacturer_id']) && count($inputs['virtuemart_manufacturer_id']) == 1) {

            if (isset($inputs['virtuemart_category_id'])) {
                $currentlink = '&virtuemart_category_id=' . (int)reset($inputs['virtuemart_category_id']);
            } else {
                if (!empty($inputs['virtuemart_manufacturer_id'])) {
                    $currentlink = '&virtuemart_manufacturer_id=' . (int)reset($inputs['virtuemart_manufacturer_id']);
                }
            }
        }



        if (!empty($currentlink)) {
            // Route::TLS_IGNORE introduced in 3.9.7
            $tls = defined("Route::TLS_IGNORE") ? Route::TLS_IGNORE : 0;
            $canonical_url = Route::_('index.php?option=com_virtuemart&view=category' . $currentlink, true,
                $tls, true);

            // Do not set a canonical if there is no menu item set for such VM page
            if (strpos($canonical_url, '/component/virtuemart/') !== false) {
                return $this;
            }

            foreach ($this->document->_links as $key => $link) {
                if (is_array($link)
                    && array_key_exists('relation', $link)
                    && !empty($link['relation']) && $link['relation'] == 'canonical') {
                    // found it - delete the old
                    unset($this->document->_links[$key]);
                }
            }

            $seoTools = new seoTools();
		    if ( $seoTools->checkOffFilters( $inputs ) )
            {
                $this->document->setMetaData('robots' , 'noindex,follow' );

            }else{
                if (!empty( $this->document->base ) )
                {
                    $canonical_url = $this->document->base ;
                }#END IF
            }#END IF




            // add a new one
            $this->document->_links[$canonical_url] = array(
                'relType' => 'rel',
                'relation' => 'canonical',
                'attribs' => ''
            );
        }
        return $this;
    }

    /**
     * Load external files if they miss
     *
     * @return CustomfiltersViewProducts
     */
    public function load()
    {
        if ($this->show_prices == 1 && !class_exists('calculationHelper')) {
            require(JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'calculationh.php');
        }

        if (!class_exists('CurrencyDisplay')) {
            require(JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'currencydisplay.php');
        }

        if (!class_exists('shopFunctionsF')) {
            require(JPATH_VM_SITE . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'shopfunctionsf.php');
        }

        if (!class_exists('VirtueMartModelCategory')) {
            require(JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'category.php');
        }

        if (!class_exists('VmImage') && file_exists(JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'image.php')) {
            require(JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'image.php');
        }

        return $this;
    }

    /**
     * Set variables from the config params
     *
     * @return CustomfiltersViewProducts
     */
    protected function setVariablesFromParams()
    {
        $params = [
            'itemid' => '',
            'categorylayout' => VmConfig::get('categorylayout', 0),
            'show_store_desc' => VmConfig::get('show_store_desc', 1),
            'show_pcustoms' => VmConfig::get('show_pcustoms', 1),
            'showcategory_desc' => VmConfig::get('showcategory_desc', 1),
            'showcategory' => VmConfig::get('showcategory', 1),
            'categories_per_row' => VmConfig::get('categories_per_row', 3),
            'showproducts' => true,
            'showsearch' => false,
            'keyword' => false,
            'productsublayout' => VmConfig::get('productsublayout', 0),
            'products_per_row' => $this->menuParams->get('prod_per_row', 3),
            'featured' => VmConfig::get('featured', 0),
            'featured_rows' => VmConfig::get('featured_rows', 1),
            'discontinued' => VmConfig::get('discontinued', 0),
            'discontinued_rows' => VmConfig::get('discontinued_rows', 1),
            'latest' => VmConfig::get('latest', 0),
            'latest_rows' => VmConfig::get('latest_rows', 1),
            'topten' => VmConfig::get('topten', 0),
            'topten_rows' => VmConfig::get('topten_rows', 1),
            'recent' => VmConfig::get('recent', 0),
            'recent_rows' => VmConfig::get('recent_rows', 1)
        ];

        foreach ($params as $param => $value) {
            //these params cannot change
            if ($param == 'showproducts' || $param == 'showsearch' || $param == 'keyword') {
                $this->$param = $value;
                continue;
            }

            if (empty($this->categoryId) || empty($this->category->$param)) {
                $this->$param = $this->menuParams->get($param, $value);
            } else {
                if (isset($this->category->$param)) {
                    $this->$param = $this->category->$param;
                }
            }
        }
        return $this;
    }
}
