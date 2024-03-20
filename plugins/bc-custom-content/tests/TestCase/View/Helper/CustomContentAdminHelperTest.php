<?php

namespace BcCustomContent\Test\TestCase\View\Helper;

use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\TestSuite\BcTestCase;
use BcCustomContent\Service\CustomEntriesServiceInterface;
use BcCustomContent\Service\CustomTablesServiceInterface;
use BcCustomContent\Test\Factory\CustomLinkFactory;
use BcCustomContent\Test\Scenario\CustomContentsScenario;
use BcCustomContent\Test\Scenario\CustomEntriesScenario;
use BcCustomContent\View\Helper\CustomContentAdminHelper;
use Cake\View\View;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class CustomContentAdminHelperTest extends BcTestCase
{

    use ScenarioAwareTrait;
    /**
     * setUp
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $view = new View($this->getRequest());
        $this->CustomContentAdminHelper = new CustomContentAdminHelper($view);
    }
    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test isDisplayEntryList
     */
    public function test_isDisplayEntryList()
    {
        //customField is null
        $customLink = CustomLinkFactory::make([
            'name' => 'test custom link',
            'status' => 1,
            'custom_table_id' => 1
        ])->getEntity();
        $rs = $this->CustomContentAdminHelper->isDisplayEntryList($customLink);
        $this->assertFalse($rs);
        //customField is not null and empty children
        $customLink = CustomLinkFactory::make([
            'name' => 'test custom link',
            'status' => 1,
            'custom_table_id' => 1,
            'display_admin_list' => 1,
            'custom_field' => [
                'name' => 'test custom field',
                'status' => 1,
                'custom_table_id' => 1,
                'type' => 'group',
            ]
        ])->getEntity();
        $rs = $this->CustomContentAdminHelper->isDisplayEntryList($customLink);
        $this->assertFalse($rs);
        //customField is not null and not empty children
        $customLink = CustomLinkFactory::make([
            'name' => 'test custom link',
            'status' => 1,
            'custom_table_id' => 1,
            'display_admin_list' => 1,
            'custom_field' => [
                'name' => 'test custom field',
                'status' => 1,
                'custom_table_id' => 1,
                'type' => 'group',
            ],
            'children' => [
                'name' => 'test custom link',
                'status' => 1,
                'custom_table_id' => 1,
                'display_admin_list' => 1,
            ]
        ])->getEntity();
        $rs = $this->CustomContentAdminHelper->isDisplayEntryList($customLink);
        $this->assertTrue($rs);
    }

    /**
     * test getFieldName
     */
    public function test_getFieldName()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }

    /**
     * test label
     */
    public function test_label()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }

    /**
     * test required
     */
    public function test_required()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }
    /**
     * test attention
     */
    public function test_attention()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }

    /*
     * test preview
     */
    public function test_preview()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }

    /*
     * test getEntryIndexTitle
     */
    public function test_getEntryIndexTitle()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }

    /*
     * test description
     */
    public function test_description()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }

    /*
     * test beforeHead
     */
    public function test_beforeHead()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }

    /*
     * test afterHead
     */
    public function test_afterHead()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }

    /**
     * test isAllowPublishEntry
     */
    public function test_isAllowPublishEntry()
    {
        //サービスクラス
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        $customEntriesService = $this->getService(CustomEntriesServiceInterface::class);
        //カスタムテーブルとカスタムエントリテーブルを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_categories',
            'title' => 'title',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);
        //データ生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomEntriesScenario::class);
        $customEntriesService->setup(1);
        //case customEntry exists
        $rs = $this->CustomContentAdminHelper->isAllowPublishEntry($customEntriesService->get(1));
        $this->assertTrue($rs);
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

}