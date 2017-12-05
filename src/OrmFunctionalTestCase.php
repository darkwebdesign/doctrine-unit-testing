<?php

namespace DarkWebDesign\DoctrineUnitTesting;

use Doctrine\DBAL\Types\Type;
use DarkWebDesign\DoctrineUnitTesting\EventListener\CacheMetadataListener;
use Doctrine\ORM\Cache\Logging\StatisticsCacheLogger;
use Doctrine\ORM\Cache\DefaultCacheFactory;

/**
 * Base testcase class for all functional ORM testcases.
 *
 * @since 2.0
 */
abstract class OrmFunctionalTestCase extends OrmTestCase
{
    /**
     * The metadata cache shared between all functional tests.
     *
     * @var \Doctrine\Common\Cache\Cache|null
     */
    private static $_metadataCacheImpl = null;

    /**
     * The query cache shared between all functional tests.
     *
     * @var \Doctrine\Common\Cache\Cache|null
     */
    private static $_queryCacheImpl = null;

    /**
     * Shared connection when a TestCase is run alone (outside of its functional suite).
     *
     * @var \Doctrine\DBAL\Connection|null
     */
    protected static $_sharedConn;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $_em;

    /**
     * @var \Doctrine\ORM\Tools\SchemaTool
     */
    protected $_schemaTool;

    /**
     * @var \Doctrine\DBAL\Logging\DebugStack
     */
    protected $_sqlLoggerStack;

    /**
     * The names of the model sets used in this testcase.
     *
     * @var array
     */
    protected $_usedModelSets = array();

    /**
     * Whether the database schema has already been created.
     *
     * @var array
     */
    protected static $_tablesCreated = array();

    /**
     * Array of entity class name to their tables that were created.
     *
     * @var array
     */
    protected static $_entityTablesCreated = array();

    /**
     * List of model sets and their classes.
     *
     * @var array
     */
    protected static $_modelSets = array(
        'cms' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\CMS\CmsUser',
            'DarkWebDesign\DoctrineUnitTesting\Models\CMS\CmsPhonenumber',
            'DarkWebDesign\DoctrineUnitTesting\Models\CMS\CmsAddress',
            'DarkWebDesign\DoctrineUnitTesting\Models\CMS\CmsEmail',
            'DarkWebDesign\DoctrineUnitTesting\Models\CMS\CmsGroup',
            'DarkWebDesign\DoctrineUnitTesting\Models\CMS\CmsArticle',
            'DarkWebDesign\DoctrineUnitTesting\Models\CMS\CmsComment',
        ),
        'forum' => array(),
        'company' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\Company\CompanyPerson',
            'DarkWebDesign\DoctrineUnitTesting\Models\Company\CompanyEmployee',
            'DarkWebDesign\DoctrineUnitTesting\Models\Company\CompanyManager',
            'DarkWebDesign\DoctrineUnitTesting\Models\Company\CompanyOrganization',
            'DarkWebDesign\DoctrineUnitTesting\Models\Company\CompanyEvent',
            'DarkWebDesign\DoctrineUnitTesting\Models\Company\CompanyAuction',
            'DarkWebDesign\DoctrineUnitTesting\Models\Company\CompanyRaffle',
            'DarkWebDesign\DoctrineUnitTesting\Models\Company\CompanyCar',
            'DarkWebDesign\DoctrineUnitTesting\Models\Company\CompanyContract',
        ),
        'ecommerce' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\ECommerce\ECommerceCart',
            'DarkWebDesign\DoctrineUnitTesting\Models\ECommerce\ECommerceCustomer',
            'DarkWebDesign\DoctrineUnitTesting\Models\ECommerce\ECommerceProduct',
            'DarkWebDesign\DoctrineUnitTesting\Models\ECommerce\ECommerceShipping',
            'DarkWebDesign\DoctrineUnitTesting\Models\ECommerce\ECommerceFeature',
            'DarkWebDesign\DoctrineUnitTesting\Models\ECommerce\ECommerceCategory'
        ),
        'generic' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\Generic\BooleanModel',
            'DarkWebDesign\DoctrineUnitTesting\Models\Generic\DateTimeModel',
            'DarkWebDesign\DoctrineUnitTesting\Models\Generic\DecimalModel',
            'DarkWebDesign\DoctrineUnitTesting\Models\Generic\SerializationModel',
        ),
        'routing' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\Routing\RoutingLeg',
            'DarkWebDesign\DoctrineUnitTesting\Models\Routing\RoutingLocation',
            'DarkWebDesign\DoctrineUnitTesting\Models\Routing\RoutingRoute',
            'DarkWebDesign\DoctrineUnitTesting\Models\Routing\RoutingRouteBooking',
        ),
        'navigation' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\Navigation\NavUser',
            'DarkWebDesign\DoctrineUnitTesting\Models\Navigation\NavCountry',
            'DarkWebDesign\DoctrineUnitTesting\Models\Navigation\NavPhotos',
            'DarkWebDesign\DoctrineUnitTesting\Models\Navigation\NavTour',
            'DarkWebDesign\DoctrineUnitTesting\Models\Navigation\NavPointOfInterest',
        ),
        'directorytree' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\DirectoryTree\AbstractContentItem',
            'DarkWebDesign\DoctrineUnitTesting\Models\DirectoryTree\File',
            'DarkWebDesign\DoctrineUnitTesting\Models\DirectoryTree\Directory',
        ),
        'ddc117' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\DDC117\DDC117Article',
            'DarkWebDesign\DoctrineUnitTesting\Models\DDC117\DDC117Reference',
            'DarkWebDesign\DoctrineUnitTesting\Models\DDC117\DDC117Translation',
            'DarkWebDesign\DoctrineUnitTesting\Models\DDC117\DDC117ArticleDetails',
            'DarkWebDesign\DoctrineUnitTesting\Models\DDC117\DDC117ApproveChanges',
            'DarkWebDesign\DoctrineUnitTesting\Models\DDC117\DDC117Editor',
            'DarkWebDesign\DoctrineUnitTesting\Models\DDC117\DDC117Link',
        ),
        'ddc3699' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\DDC3699\DDC3699Parent',
            'DarkWebDesign\DoctrineUnitTesting\Models\DDC3699\DDC3699RelationOne',
            'DarkWebDesign\DoctrineUnitTesting\Models\DDC3699\DDC3699RelationMany',
            'DarkWebDesign\DoctrineUnitTesting\Models\DDC3699\DDC3699Child',
        ),
        'stockexchange' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\StockExchange\Bond',
            'DarkWebDesign\DoctrineUnitTesting\Models\StockExchange\Stock',
            'DarkWebDesign\DoctrineUnitTesting\Models\StockExchange\Market',
        ),
        'legacy' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\Legacy\LegacyUser',
            'DarkWebDesign\DoctrineUnitTesting\Models\Legacy\LegacyUserReference',
            'DarkWebDesign\DoctrineUnitTesting\Models\Legacy\LegacyArticle',
            'DarkWebDesign\DoctrineUnitTesting\Models\Legacy\LegacyCar',
        ),
        'customtype' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\CustomType\CustomTypeChild',
            'DarkWebDesign\DoctrineUnitTesting\Models\CustomType\CustomTypeParent',
            'DarkWebDesign\DoctrineUnitTesting\Models\CustomType\CustomTypeUpperCase',
        ),
        'compositekeyinheritance' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\CompositeKeyInheritance\JoinedRootClass',
            'DarkWebDesign\DoctrineUnitTesting\Models\CompositeKeyInheritance\JoinedChildClass',
            'DarkWebDesign\DoctrineUnitTesting\Models\CompositeKeyInheritance\SingleRootClass',
            'DarkWebDesign\DoctrineUnitTesting\Models\CompositeKeyInheritance\SingleChildClass',
        ),
        'taxi' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\Taxi\PaidRide',
            'DarkWebDesign\DoctrineUnitTesting\Models\Taxi\Ride',
            'DarkWebDesign\DoctrineUnitTesting\Models\Taxi\Car',
            'DarkWebDesign\DoctrineUnitTesting\Models\Taxi\Driver',
        ),
        'cache' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\Cache\Country',
            'DarkWebDesign\DoctrineUnitTesting\Models\Cache\State',
            'DarkWebDesign\DoctrineUnitTesting\Models\Cache\City',
            'DarkWebDesign\DoctrineUnitTesting\Models\Cache\Traveler',
            'DarkWebDesign\DoctrineUnitTesting\Models\Cache\TravelerProfileInfo',
            'DarkWebDesign\DoctrineUnitTesting\Models\Cache\TravelerProfile',
            'DarkWebDesign\DoctrineUnitTesting\Models\Cache\Travel',
            'DarkWebDesign\DoctrineUnitTesting\Models\Cache\Attraction',
            'DarkWebDesign\DoctrineUnitTesting\Models\Cache\Restaurant',
            'DarkWebDesign\DoctrineUnitTesting\Models\Cache\Beach',
            'DarkWebDesign\DoctrineUnitTesting\Models\Cache\Bar',
            'DarkWebDesign\DoctrineUnitTesting\Models\Cache\Flight',
            'DarkWebDesign\DoctrineUnitTesting\Models\Cache\Token',
            'DarkWebDesign\DoctrineUnitTesting\Models\Cache\Login',
            'DarkWebDesign\DoctrineUnitTesting\Models\Cache\Client',
            'DarkWebDesign\DoctrineUnitTesting\Models\Cache\Action',
            'DarkWebDesign\DoctrineUnitTesting\Models\Cache\ComplexAction',
            'DarkWebDesign\DoctrineUnitTesting\Models\Cache\AttractionInfo',
            'DarkWebDesign\DoctrineUnitTesting\Models\Cache\AttractionContactInfo',
            'DarkWebDesign\DoctrineUnitTesting\Models\Cache\AttractionLocationInfo'
        ),
        'tweet' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\Tweet\User',
            'DarkWebDesign\DoctrineUnitTesting\Models\Tweet\Tweet',
            'DarkWebDesign\DoctrineUnitTesting\Models\Tweet\UserList',
        ),
        'ddc2504' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\DDC2504\DDC2504RootClass',
            'DarkWebDesign\DoctrineUnitTesting\Models\DDC2504\DDC2504ChildClass',
            'DarkWebDesign\DoctrineUnitTesting\Models\DDC2504\DDC2504OtherClass',
        ),
        'ddc3346' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\DDC3346\DDC3346Author',
            'DarkWebDesign\DoctrineUnitTesting\Models\DDC3346\DDC3346Article',
        ),
        'quote' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\Quote\Address',
            'DarkWebDesign\DoctrineUnitTesting\Models\Quote\Group',
            'DarkWebDesign\DoctrineUnitTesting\Models\Quote\NumericEntity',
            'DarkWebDesign\DoctrineUnitTesting\Models\Quote\Phone',
            'DarkWebDesign\DoctrineUnitTesting\Models\Quote\User'
        ),
        'vct_onetoone' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\ValueConversionType\InversedOneToOneEntity',
            'DarkWebDesign\DoctrineUnitTesting\Models\ValueConversionType\OwningOneToOneEntity'
        ),
        'vct_onetoone_compositeid' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\ValueConversionType\InversedOneToOneCompositeIdEntity',
            'DarkWebDesign\DoctrineUnitTesting\Models\ValueConversionType\OwningOneToOneCompositeIdEntity'
        ),
        'vct_onetoone_compositeid_foreignkey' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\ValueConversionType\AuxiliaryEntity',
            'DarkWebDesign\DoctrineUnitTesting\Models\ValueConversionType\InversedOneToOneCompositeIdForeignKeyEntity',
            'DarkWebDesign\DoctrineUnitTesting\Models\ValueConversionType\OwningOneToOneCompositeIdForeignKeyEntity'
        ),
        'vct_onetomany' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\ValueConversionType\InversedOneToManyEntity',
            'DarkWebDesign\DoctrineUnitTesting\Models\ValueConversionType\OwningManyToOneEntity'
        ),
        'vct_onetomany_compositeid' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\ValueConversionType\InversedOneToManyCompositeIdEntity',
            'DarkWebDesign\DoctrineUnitTesting\Models\ValueConversionType\OwningManyToOneCompositeIdEntity'
        ),
        'vct_onetomany_compositeid_foreignkey' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\ValueConversionType\AuxiliaryEntity',
            'DarkWebDesign\DoctrineUnitTesting\Models\ValueConversionType\InversedOneToManyCompositeIdForeignKeyEntity',
            'DarkWebDesign\DoctrineUnitTesting\Models\ValueConversionType\OwningManyToOneCompositeIdForeignKeyEntity'
        ),
        'vct_onetomany_extralazy' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\ValueConversionType\InversedOneToManyExtraLazyEntity',
            'DarkWebDesign\DoctrineUnitTesting\Models\ValueConversionType\OwningManyToOneExtraLazyEntity'
        ),
        'vct_manytomany' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\ValueConversionType\InversedManyToManyEntity',
            'DarkWebDesign\DoctrineUnitTesting\Models\ValueConversionType\OwningManyToManyEntity'
        ),
        'vct_manytomany_compositeid' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\ValueConversionType\InversedManyToManyCompositeIdEntity',
            'DarkWebDesign\DoctrineUnitTesting\Models\ValueConversionType\OwningManyToManyCompositeIdEntity'
        ),
        'vct_manytomany_compositeid_foreignkey' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\ValueConversionType\AuxiliaryEntity',
            'DarkWebDesign\DoctrineUnitTesting\Models\ValueConversionType\InversedManyToManyCompositeIdForeignKeyEntity',
            'DarkWebDesign\DoctrineUnitTesting\Models\ValueConversionType\OwningManyToManyCompositeIdForeignKeyEntity'
        ),
        'vct_manytomany_extralazy' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\ValueConversionType\InversedManyToManyExtraLazyEntity',
            'DarkWebDesign\DoctrineUnitTesting\Models\ValueConversionType\OwningManyToManyExtraLazyEntity'
        ),
        'geonames' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\GeoNames\Country',
            'DarkWebDesign\DoctrineUnitTesting\Models\GeoNames\Admin1',
            'DarkWebDesign\DoctrineUnitTesting\Models\GeoNames\Admin1AlternateName',
            'DarkWebDesign\DoctrineUnitTesting\Models\GeoNames\City'
        ),
        'custom_id_object_type' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\CustomType\CustomIdObjectTypeParent',
            'DarkWebDesign\DoctrineUnitTesting\Models\CustomType\CustomIdObjectTypeChild',
        ),
        'pagination' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\Pagination\Company',
            'DarkWebDesign\DoctrineUnitTesting\Models\Pagination\Logo',
            'DarkWebDesign\DoctrineUnitTesting\Models\Pagination\Department',
            'DarkWebDesign\DoctrineUnitTesting\Models\Pagination\User',
            'DarkWebDesign\DoctrineUnitTesting\Models\Pagination\User1',
        ),
        'versioned_many_to_one' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\VersionedManyToOne\Category',
            'DarkWebDesign\DoctrineUnitTesting\Models\VersionedManyToOne\Article',
        ),
        'issue5989' => array(
            'DarkWebDesign\DoctrineUnitTesting\Models\Issue5989\Issue5989Person',
            'DarkWebDesign\DoctrineUnitTesting\Models\Issue5989\Issue5989Employee',
            'DarkWebDesign\DoctrineUnitTesting\Models\Issue5989\Issue5989Manager',
        ),
    );

    /**
     * @param string $setName
     *
     * @return void
     */
    protected function useModelSet($setName)
    {
        $this->_usedModelSets[$setName] = true;
    }

    /**
     * Sweeps the database tables and clears the EntityManager.
     *
     * @return void
     */
    protected function tearDown()
    {
        $conn     = static::$_sharedConn;

        // In case test is skipped, tearDown is called, but no setup may have run
        if ( ! $conn) {
            return;
        }

        $platform = $conn->getDatabasePlatform();

        $this->_sqlLoggerStack->enabled = false;

        if (isset($this->_usedModelSets['cms'])) {
            $conn->executeUpdate('DELETE FROM cms_users_groups');
            $conn->executeUpdate('DELETE FROM cms_groups');
            $conn->executeUpdate('DELETE FROM cms_addresses');
            $conn->executeUpdate('DELETE FROM cms_phonenumbers');
            $conn->executeUpdate('DELETE FROM cms_comments');
            $conn->executeUpdate('DELETE FROM cms_articles');
            $conn->executeUpdate('DELETE FROM cms_users');
            $conn->executeUpdate('DELETE FROM cms_emails');
        }

        if (isset($this->_usedModelSets['ecommerce'])) {
            $conn->executeUpdate('DELETE FROM ecommerce_carts_products');
            $conn->executeUpdate('DELETE FROM ecommerce_products_categories');
            $conn->executeUpdate('DELETE FROM ecommerce_products_related');
            $conn->executeUpdate('DELETE FROM ecommerce_carts');
            $conn->executeUpdate('DELETE FROM ecommerce_customers');
            $conn->executeUpdate('DELETE FROM ecommerce_features');
            $conn->executeUpdate('DELETE FROM ecommerce_products');
            $conn->executeUpdate('DELETE FROM ecommerce_shippings');
            $conn->executeUpdate('UPDATE ecommerce_categories SET parent_id = NULL');
            $conn->executeUpdate('DELETE FROM ecommerce_categories');
        }

        if (isset($this->_usedModelSets['company'])) {
            $conn->executeUpdate('DELETE FROM company_contract_employees');
            $conn->executeUpdate('DELETE FROM company_contract_managers');
            $conn->executeUpdate('DELETE FROM company_contracts');
            $conn->executeUpdate('DELETE FROM company_persons_friends');
            $conn->executeUpdate('DELETE FROM company_managers');
            $conn->executeUpdate('DELETE FROM company_employees');
            $conn->executeUpdate('UPDATE company_persons SET spouse_id = NULL');
            $conn->executeUpdate('DELETE FROM company_persons');
            $conn->executeUpdate('DELETE FROM company_raffles');
            $conn->executeUpdate('DELETE FROM company_auctions');
            $conn->executeUpdate('UPDATE company_organizations SET main_event_id = NULL');
            $conn->executeUpdate('DELETE FROM company_events');
            $conn->executeUpdate('DELETE FROM company_organizations');
        }

        if (isset($this->_usedModelSets['generic'])) {
            $conn->executeUpdate('DELETE FROM boolean_model');
            $conn->executeUpdate('DELETE FROM date_time_model');
            $conn->executeUpdate('DELETE FROM decimal_model');
            $conn->executeUpdate('DELETE FROM serialize_model');
        }

        if (isset($this->_usedModelSets['routing'])) {
            $conn->executeUpdate('DELETE FROM RoutingRouteLegs');
            $conn->executeUpdate('DELETE FROM RoutingRouteBooking');
            $conn->executeUpdate('DELETE FROM RoutingRoute');
            $conn->executeUpdate('DELETE FROM RoutingLeg');
            $conn->executeUpdate('DELETE FROM RoutingLocation');
        }

        if(isset($this->_usedModelSets['navigation'])) {
            $conn->executeUpdate('DELETE FROM navigation_tour_pois');
            $conn->executeUpdate('DELETE FROM navigation_photos');
            $conn->executeUpdate('DELETE FROM navigation_pois');
            $conn->executeUpdate('DELETE FROM navigation_tours');
            $conn->executeUpdate('DELETE FROM navigation_countries');
        }
        if (isset($this->_usedModelSets['directorytree'])) {
            $conn->executeUpdate('DELETE FROM ' . $platform->quoteIdentifier("file"));
            // MySQL doesn't know deferred deletions therefore only executing the second query gives errors.
            $conn->executeUpdate('DELETE FROM Directory WHERE parentDirectory_id IS NOT NULL');
            $conn->executeUpdate('DELETE FROM Directory');
        }
        if (isset($this->_usedModelSets['ddc117'])) {
            $conn->executeUpdate('DELETE FROM ddc117editor_ddc117translation');
            $conn->executeUpdate('DELETE FROM DDC117Editor');
            $conn->executeUpdate('DELETE FROM DDC117ApproveChanges');
            $conn->executeUpdate('DELETE FROM DDC117Link');
            $conn->executeUpdate('DELETE FROM DDC117Reference');
            $conn->executeUpdate('DELETE FROM DDC117ArticleDetails');
            $conn->executeUpdate('DELETE FROM DDC117Translation');
            $conn->executeUpdate('DELETE FROM DDC117Article');
        }
        if (isset($this->_usedModelSets['stockexchange'])) {
            $conn->executeUpdate('DELETE FROM exchange_bonds_stocks');
            $conn->executeUpdate('DELETE FROM exchange_bonds');
            $conn->executeUpdate('DELETE FROM exchange_stocks');
            $conn->executeUpdate('DELETE FROM exchange_markets');
        }
        if (isset($this->_usedModelSets['legacy'])) {
            $conn->executeUpdate('DELETE FROM legacy_users_cars');
            $conn->executeUpdate('DELETE FROM legacy_users_reference');
            $conn->executeUpdate('DELETE FROM legacy_articles');
            $conn->executeUpdate('DELETE FROM legacy_cars');
            $conn->executeUpdate('DELETE FROM legacy_users');
        }

        if (isset($this->_usedModelSets['customtype'])) {
            $conn->executeUpdate('DELETE FROM customtype_parent_friends');
            $conn->executeUpdate('DELETE FROM customtype_parents');
            $conn->executeUpdate('DELETE FROM customtype_children');
            $conn->executeUpdate('DELETE FROM customtype_uppercases');
        }

        if (isset($this->_usedModelSets['compositekeyinheritance'])) {
            $conn->executeUpdate('DELETE FROM JoinedChildClass');
            $conn->executeUpdate('DELETE FROM JoinedRootClass');
            $conn->executeUpdate('DELETE FROM SingleRootClass');
        }

        if (isset($this->_usedModelSets['taxi'])) {
            $conn->executeUpdate('DELETE FROM taxi_paid_ride');
            $conn->executeUpdate('DELETE FROM taxi_ride');
            $conn->executeUpdate('DELETE FROM taxi_car');
            $conn->executeUpdate('DELETE FROM taxi_driver');
        }

        if (isset($this->_usedModelSets['tweet'])) {
            $conn->executeUpdate('DELETE FROM tweet_tweet');
            $conn->executeUpdate('DELETE FROM tweet_user_list');
            $conn->executeUpdate('DELETE FROM tweet_user');
        }

        if (isset($this->_usedModelSets['cache'])) {
            $conn->executeUpdate('DELETE FROM cache_attraction_location_info');
            $conn->executeUpdate('DELETE FROM cache_attraction_contact_info');
            $conn->executeUpdate('DELETE FROM cache_attraction_info');
            $conn->executeUpdate('DELETE FROM cache_visited_cities');
            $conn->executeUpdate('DELETE FROM cache_flight');
            $conn->executeUpdate('DELETE FROM cache_attraction');
            $conn->executeUpdate('DELETE FROM cache_travel');
            $conn->executeUpdate('DELETE FROM cache_traveler');
            $conn->executeUpdate('DELETE FROM cache_traveler_profile_info');
            $conn->executeUpdate('DELETE FROM cache_traveler_profile');
            $conn->executeUpdate('DELETE FROM cache_city');
            $conn->executeUpdate('DELETE FROM cache_state');
            $conn->executeUpdate('DELETE FROM cache_country');
            $conn->executeUpdate('DELETE FROM cache_login');
            $conn->executeUpdate('DELETE FROM cache_complex_action');
            $conn->executeUpdate('DELETE FROM cache_token');
            $conn->executeUpdate('DELETE FROM cache_action');
            $conn->executeUpdate('DELETE FROM cache_client');
        }

        if (isset($this->_usedModelSets['ddc3346'])) {
            $conn->executeUpdate('DELETE FROM ddc3346_articles');
            $conn->executeUpdate('DELETE FROM ddc3346_users');
        }

        if (isset($this->_usedModelSets['quote'])) {
            $conn->executeUpdate('DELETE FROM ' . $platform->quoteIdentifier("quote-address"));
            $conn->executeUpdate('DELETE FROM ' . $platform->quoteIdentifier("quote-group"));
            $conn->executeUpdate('DELETE FROM ' . $platform->quoteIdentifier("quote-phone"));
            $conn->executeUpdate('DELETE FROM ' . $platform->quoteIdentifier("quote-user"));
        }

        if (isset($this->_usedModelSets['vct_onetoone'])) {
            $conn->executeUpdate('DELETE FROM vct_owning_onetoone');
            $conn->executeUpdate('DELETE FROM vct_inversed_onetoone');
        }

        if (isset($this->_usedModelSets['vct_onetoone_compositeid'])) {
            $conn->executeUpdate('DELETE FROM vct_owning_onetoone_compositeid');
            $conn->executeUpdate('DELETE FROM vct_inversed_onetoone_compositeid');
        }

        if (isset($this->_usedModelSets['vct_onetoone_compositeid_foreignkey'])) {
            $conn->executeUpdate('DELETE FROM vct_owning_onetoone_compositeid_foreignkey');
            $conn->executeUpdate('DELETE FROM vct_inversed_onetoone_compositeid_foreignkey');
            $conn->executeUpdate('DELETE FROM vct_auxiliary');
        }

        if (isset($this->_usedModelSets['vct_onetomany'])) {
            $conn->executeUpdate('DELETE FROM vct_owning_manytoone');
            $conn->executeUpdate('DELETE FROM vct_inversed_onetomany');
        }

        if (isset($this->_usedModelSets['vct_onetomany_compositeid'])) {
            $conn->executeUpdate('DELETE FROM vct_owning_manytoone_compositeid');
            $conn->executeUpdate('DELETE FROM vct_inversed_onetomany_compositeid');
        }

        if (isset($this->_usedModelSets['vct_onetomany_compositeid_foreignkey'])) {
            $conn->executeUpdate('DELETE FROM vct_owning_manytoone_compositeid_foreignkey');
            $conn->executeUpdate('DELETE FROM vct_inversed_onetomany_compositeid_foreignkey');
            $conn->executeUpdate('DELETE FROM vct_auxiliary');
        }

        if (isset($this->_usedModelSets['vct_onetomany_extralazy'])) {
            $conn->executeUpdate('DELETE FROM vct_owning_manytoone_extralazy');
            $conn->executeUpdate('DELETE FROM vct_inversed_onetomany_extralazy');
        }

        if (isset($this->_usedModelSets['vct_manytomany'])) {
            $conn->executeUpdate('DELETE FROM vct_xref_manytomany');
            $conn->executeUpdate('DELETE FROM vct_owning_manytomany');
            $conn->executeUpdate('DELETE FROM vct_inversed_manytomany');
        }

        if (isset($this->_usedModelSets['vct_manytomany_compositeid'])) {
            $conn->executeUpdate('DELETE FROM vct_xref_manytomany_compositeid');
            $conn->executeUpdate('DELETE FROM vct_owning_manytomany_compositeid');
            $conn->executeUpdate('DELETE FROM vct_inversed_manytomany_compositeid');
        }

        if (isset($this->_usedModelSets['vct_manytomany_compositeid_foreignkey'])) {
            $conn->executeUpdate('DELETE FROM vct_xref_manytomany_compositeid_foreignkey');
            $conn->executeUpdate('DELETE FROM vct_owning_manytomany_compositeid_foreignkey');
            $conn->executeUpdate('DELETE FROM vct_inversed_manytomany_compositeid_foreignkey');
            $conn->executeUpdate('DELETE FROM vct_auxiliary');
        }

        if (isset($this->_usedModelSets['vct_manytomany_extralazy'])) {
            $conn->executeUpdate('DELETE FROM vct_xref_manytomany_extralazy');
            $conn->executeUpdate('DELETE FROM vct_owning_manytomany_extralazy');
            $conn->executeUpdate('DELETE FROM vct_inversed_manytomany_extralazy');
        }
        if (isset($this->_usedModelSets['geonames'])) {
            $conn->executeUpdate('DELETE FROM geonames_admin1_alternate_name');
            $conn->executeUpdate('DELETE FROM geonames_admin1');
            $conn->executeUpdate('DELETE FROM geonames_city');
            $conn->executeUpdate('DELETE FROM geonames_country');
        }

        if (isset($this->_usedModelSets['custom_id_object_type'])) {
            $conn->executeUpdate('DELETE FROM custom_id_type_child');
            $conn->executeUpdate('DELETE FROM custom_id_type_parent');
        }

        if (isset($this->_usedModelSets['pagination'])) {
            $conn->executeUpdate('DELETE FROM pagination_logo');
            $conn->executeUpdate('DELETE FROM pagination_department');
            $conn->executeUpdate('DELETE FROM pagination_company');
            $conn->executeUpdate('DELETE FROM pagination_user');
        }

        if (isset($this->_usedModelSets['versioned_many_to_one'])) {
            $conn->executeUpdate('DELETE FROM versioned_many_to_one_article');
            $conn->executeUpdate('DELETE FROM versioned_many_to_one_category');
        }

        if (isset($this->_usedModelSets['issue5989'])) {
            $conn->executeUpdate('DELETE FROM issue5989_persons');
            $conn->executeUpdate('DELETE FROM issue5989_employees');
            $conn->executeUpdate('DELETE FROM issue5989_managers');
        }

        $this->_em->clear();
    }

    /**
     * @param array $classNames
     *
     * @return void
     *
     * @throws \RuntimeException
     */
    protected function setUpEntitySchema(array $classNames)
    {
        if ($this->_em === null) {
            throw new \RuntimeException("EntityManager not set, you have to call parent::setUp() before invoking this method.");
        }

        $classes = array();
        foreach ($classNames as $className) {
            if ( ! isset(static::$_entityTablesCreated[$className])) {
                static::$_entityTablesCreated[$className] = true;
                $classes[] = $this->_em->getClassMetadata($className);
            }
        }

        if ($classes) {
            $this->_schemaTool->createSchema($classes);
        }
    }

    /**
     * Creates a connection to the test database, if there is none yet, and
     * creates the necessary tables.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->setUpDBALTypes();

        $forceCreateTables = false;

        if ( ! isset(static::$_sharedConn)) {
            static::$_sharedConn = TestUtil::getConnection();

            if (static::$_sharedConn->getDriver() instanceof \Doctrine\DBAL\Driver\PDOSqlite\Driver) {
                $forceCreateTables = true;
            }
        }

        if (isset($GLOBALS['DOCTRINE_MARK_SQL_LOGS'])) {
            if (in_array(static::$_sharedConn->getDatabasePlatform()->getName(), array("mysql", "postgresql"))) {
                static::$_sharedConn->executeQuery('SELECT 1 /*' . get_class($this) . '*/');
            } else if (static::$_sharedConn->getDatabasePlatform()->getName() == "oracle") {
                static::$_sharedConn->executeQuery('SELECT 1 /*' . get_class($this) . '*/ FROM dual');
            }
        }

        if ( ! $this->_em) {
            $this->_em = $this->_getEntityManager();
            $this->_schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->_em);
        }

        $classes = array();

        foreach ($this->_usedModelSets as $setName => $bool) {
            if ( ! isset(static::$_tablesCreated[$setName])/* || $forceCreateTables*/) {
                foreach (static::$_modelSets[$setName] as $className) {
                    $classes[] = $this->_em->getClassMetadata($className);
                }

                static::$_tablesCreated[$setName] = true;
            }
        }

        if ($classes) {
            $this->_schemaTool->createSchema($classes);
        }

        $this->_sqlLoggerStack->enabled = true;
    }

    /**
     * Gets an EntityManager for testing purposes.
     *
     * @param \Doctrine\ORM\Configuration   $config       The Configuration to pass to the EntityManager.
     * @param \Doctrine\Common\EventManager $eventManager The EventManager to pass to the EntityManager.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    protected function _getEntityManager($config = null, $eventManager = null) {
        // NOTE: Functional tests use their own shared metadata cache, because
        // the actual database platform used during execution has effect on some
        // metadata mapping behaviors (like the choice of the ID generation).
        if (is_null(self::$_metadataCacheImpl)) {
            if (isset($GLOBALS['DOCTRINE_CACHE_IMPL'])) {
                self::$_metadataCacheImpl = new $GLOBALS['DOCTRINE_CACHE_IMPL'];
            } else {
                self::$_metadataCacheImpl = new \Doctrine\Common\Cache\ArrayCache;
            }
        }

        if (is_null(self::$_queryCacheImpl)) {
            self::$_queryCacheImpl = new \Doctrine\Common\Cache\ArrayCache;
        }

        $this->_sqlLoggerStack = new \Doctrine\DBAL\Logging\DebugStack();
        $this->_sqlLoggerStack->enabled = false;

        //FIXME: two different configs! $conn and the created entity manager have
        // different configs.
        $config = new \Doctrine\ORM\Configuration();
        $config->setMetadataCacheImpl(self::$_metadataCacheImpl);
        $config->setQueryCacheImpl(self::$_queryCacheImpl);
        $config->setProxyDir(__DIR__ . '/Proxies');
        $config->setProxyNamespace('DarkWebDesign\DoctrineUnitTesting\Proxies');

        $enableSecondLevelCache = getenv('ENABLE_SECOND_LEVEL_CACHE');

        if ($this->isSecondLevelCacheEnabled || $enableSecondLevelCache) {

            $cacheConfig    = new \Doctrine\ORM\Cache\CacheConfiguration();
            $cache          = $this->getSharedSecondLevelCacheDriverImpl();
            $factory        = new DefaultCacheFactory($cacheConfig->getRegionsConfiguration(), $cache);

            $this->secondLevelCacheFactory = $factory;

            if ($this->isSecondLevelCacheLogEnabled) {
                $this->secondLevelCacheLogger = new StatisticsCacheLogger();
                $cacheConfig->setCacheLogger($this->secondLevelCacheLogger);
            }

            $cacheConfig->setCacheFactory($factory);
            $config->setSecondLevelCacheEnabled(true);
            $config->setSecondLevelCacheConfiguration($cacheConfig);

            $this->isSecondLevelCacheEnabled = true;
        }

        $config->setMetadataDriverImpl($config->newDefaultAnnotationDriver(array(
            realpath(__DIR__ . '/Models/Cache'),
            realpath(__DIR__ . '/Models/GeoNames')
        ), true));

        $conn = static::$_sharedConn;
        $conn->getConfiguration()->setSQLLogger($this->_sqlLoggerStack);

        // get rid of more global state
        $evm = $conn->getEventManager();
        foreach ($evm->getListeners() AS $event => $listeners) {
            foreach ($listeners AS $listener) {
                $evm->removeEventListener(array($event), $listener);
            }
        }

        if ($enableSecondLevelCache) {
            $evm->addEventListener('loadClassMetadata', new CacheMetadataListener());
        }

        if (isset($GLOBALS['db_event_subscribers'])) {
            foreach (explode(",", $GLOBALS['db_event_subscribers']) AS $subscriberClass) {
                $subscriberInstance = new $subscriberClass();
                $evm->addEventSubscriber($subscriberInstance);
            }
        }

        if (isset($GLOBALS['debug_uow_listener'])) {
            $evm->addEventListener(array('onFlush'), new \Doctrine\ORM\Tools\DebugUnitOfWorkListener());
        }

        return \Doctrine\ORM\EntityManager::create($conn, $config);
    }

    /**
     * @param \Exception $e
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function onNotSuccessfulTest(\Exception $e)
    {
        if ($e instanceof \PHPUnit_Framework_AssertionFailedError) {
            throw $e;
        }

        if(isset($this->_sqlLoggerStack->queries) && count($this->_sqlLoggerStack->queries)) {
            $queries = "";
            for($i = count($this->_sqlLoggerStack->queries)-1; $i > max(count($this->_sqlLoggerStack->queries)-25, 0) && isset($this->_sqlLoggerStack->queries[$i]); $i--) {
                $query = $this->_sqlLoggerStack->queries[$i];
                $params = array_map(function($p) { if (is_object($p)) return get_class($p); else return "'".$p."'"; }, $query['params'] ?: array());
                $queries .= ($i+1).". SQL: '".$query['sql']."' Params: ".implode(", ", $params).PHP_EOL;
            }

            $trace = $e->getTrace();
            $traceMsg = "";
            foreach($trace AS $part) {
                if(isset($part['file'])) {
                    if(strpos($part['file'], "PHPUnit/") !== false) {
                        // Beginning with PHPUnit files we don't print the trace anymore.
                        break;
                    }

                    $traceMsg .= $part['file'].":".$part['line'].PHP_EOL;
                }
            }

            $message = "[".get_class($e)."] ".$e->getMessage().PHP_EOL.PHP_EOL."With queries:".PHP_EOL.$queries.PHP_EOL."Trace:".PHP_EOL.$traceMsg;

            throw new \Exception($message, (int)$e->getCode(), $e);
        }
        throw $e;
    }

    public function assertSQLEquals($expectedSql, $actualSql)
    {
        return $this->assertEquals(strtolower($expectedSql), strtolower($actualSql), "Lowercase comparison of SQL statements failed.");
    }

    /**
     * Using the SQL Logger Stack this method retrieves the current query count executed in this test.
     *
     * @return int
     */
    protected function getCurrentQueryCount()
    {
        return count($this->_sqlLoggerStack->queries);
    }

    /**
     * Configures DBAL types required in tests
     */
    protected function setUpDBALTypes()
    {
        if (Type::hasType('rot13')) {
            Type::overrideType('rot13', 'DarkWebDesign\DoctrineUnitTesting\DbalTypes\Rot13Type');
        } else {
            Type::addType('rot13', 'DarkWebDesign\DoctrineUnitTesting\DbalTypes\Rot13Type');
        }
    }
}
