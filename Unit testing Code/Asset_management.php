<?php

use Tests\TestCase;
use App\Models\AssetGroup;
use App\Models\AssetSubGroup;
use App\Models\FinancialYear;
use App\Models\ScheduleType;
use App\Models\DepreciationRate;
use App\Models\AssetPrefix;
use App\Models\Asset;
use App\Models\User;
use App\Models\MaintenanceRecord;
use App\Models\Depreciation;
use App\Models\AllocationHistory; 
use App\Models\Report; 

class AssetManagementTest extends TestCase
{
    /**
     * Test asset group setup
     */
    public function test_assetGroupSetup()
    {
        $group = new AssetGroup();
        $group->id = 1; // Replace with a valid ID if needed
        $result = false;
        try {
            $group->update(['name' => 'Updated Asset Group Name']);
            $result = true;
        } catch (Exception $e) {
            // Handle exception
        }
        $this->assertTrue($result);
    }

    /**
     * Test asset sub-group setup
     */
    public function test_assetSubGroupSetup()
    {
        $subGroup = new AssetSubGroup();
        $subGroup->id = 1;
        $result = false;
        try {
            $subGroup->update(['name' => 'Updated Asset Sub-Group Name']);
            $result = true;
        } catch (Exception $e) {
            // Handle exception
        }
        $this->assertTrue($result);
    }

    /**
     * Test financial year setup
     */
    public function test_financialYearSetup()
    {
        $financialYear = new FinancialYear();
        $financialYear->id = 1;
        $result = false;
        try {
            $financialYear->update(['year' => '2023']);
            $result = true;
        } catch (Exception $e) {
            // Handle exception
        }
        $this->assertTrue($result);
    }

    /**
     * Test schedule type setup
     */
    public function test_scheduleTypeSetup()
    {
        $scheduleType = new ScheduleType();
        $scheduleType->id = 1;
        $result = false;
        try {
            $scheduleType->update(['type' => 'Updated Schedule Type']);
            $result = true;
        } catch (Exception $e) {
            // Handle exception
        }
        $this->assertTrue($result);
    }

    /**
     * Test depreciation rate setup
     */
    public function test_depreciationRateSetup()
    {
        $depreciationRate = new DepreciationRate();
        $depreciationRate->id = 1;
        $result = false;
        try {
            $depreciationRate->update(['rate' => 5.0]);
            $result = true;
        } catch (Exception $e) {
            // Handle exception
        }
        $this->assertTrue($result);
    }

    /**
     * Test asset prefix code setup
     */
    public function test_assetPrefixCodeSetup()
    {
        $prefix = new AssetPrefix();
        $prefix->id = 1;
        $result = false;
        try {
            $prefix->update(['code' => 'PREFIX123']);
            $result = true;
        } catch (Exception $e) {
            // Handle exception
        }
        $this->assertTrue($result);
    }


    /**
     * Test asset registration
     */
    public function test_assetRegistration()
    {
        $assetData = [
            'name' => 'New Asset',
            'category' => 'Electronics',
            'purchase_date' => '2024-01-01',
            'cost' => 1000.00,
        ];
        
        $asset = Asset::create($assetData);
        $this->assertDatabaseHas('assets', $assetData);
    }

    /**
     * Test asset list
     */
    public function test_assetList()
    {
        // Assume assets are already created
        $response = $this->get('/assets');
        $response->assertStatus(200);
        $response->assertViewHas('assets'); // Check if assets are passed to the view
    }

    /**
     * Test asset allocation
     */
    public function test_assetAllocation()
    {
        $asset = Asset::factory()->create(); // Create an asset using a factory
        $user = User::factory()->create(); // Create a user using a factory
        
        $asset->allocateTo($user->id); // Allocate asset to the user
        
        // Verify allocation
        $this->assertEquals($user->id, $asset->fresh()->allocated_to);
    }

    /**
     * Test asset deallocation
     */
    public function test_assetDeallocation()
    {
        $asset = Asset::factory()->create(['allocated_to' => 1]); // Create an allocated asset
        
        $asset->deallocate(); // Deallocate the asset
        
        // Verify deallocation
        $this->assertNull($asset->fresh()->allocated_to);
    }

    /**
     * Test asset tracking
     */
    public function test_assetTracking()
    {
        $asset = Asset::factory()->create(['location' => 'Warehouse A']); // Create an asset
        
        $asset->updateLocation('Warehouse B'); // Update the location
        
        // Verify location update
        $this->assertEquals('Warehouse B', $asset->fresh()->location);
    }

    /**
     * Test maintenance list management
     */
    public function test_maintenanceList()
    {
        $asset = Asset::factory()->create(); // Create an asset
        $maintenanceData = [
            'description' => 'Annual check-up',
            'date' => '2024-06-01',
            'asset_id' => $asset->id // Associate maintenance with the asset
        ];
        
        // Create a maintenance record for the asset
        $maintenanceRecord = MaintenanceRecord::create($maintenanceData);
        
        // Verify that the maintenance record was created
        $this->assertDatabaseHas('maintenance_records', $maintenanceData);
    }

    public function test_depreciationProcess()
    {
        $asset = Asset::factory()->create(['cost' => 1000]);
        $depreciationRate = 10; // 10%
        
        $depreciation = new Depreciation();
        $depreciation->process($asset->id, $depreciationRate);
        
        // Assuming process method updates the asset's value
        $this->assertEquals(900, $asset->fresh()->value); // 1000 - 10%
    }

    /**
     * Test allocation history report
     */
    public function test_allocationHistoryReport()
    {
        $asset = Asset::factory()->create();
        $user = User::factory()->create();

        $asset->allocateTo($user->id);
        
        $report = AllocationHistory::generateReport($asset->id); // Assuming you have this method
        
        $this->assertNotEmpty($report);
        $this->assertEquals($asset->id, $report->asset_id);
    }

    /**
     * Test asset addition report
     */
    public function test_assetAdditionReport()
    {
        $assetData = [
            'name' => 'New Asset',
            'category' => 'Electronics',
            'purchase_date' => '2024-01-01',
            'cost' => 1000.00,
        ];
        
        Asset::create($assetData);
        
        $report = Report::generateAssetAdditionReport(); // Assuming this method exists
        $this->assertContains($assetData['name'], $report->assets->pluck('name')->toArray());
    }

    /**
     * Test asset deletion report
     */
    public function test_assetDeletionReport()
    {
        $asset = Asset::factory()->create();
        $asset->delete();
        
        $report = Report::generateAssetDeletionReport(); // Assuming this method exists
        $this->assertContains($asset->id, $report->deleted_assets->pluck('id')->toArray());
    }

    /**
     * Test asset history report
     */
    public function test_assetHistoryReport()
    {
        $asset = Asset::factory()->create();
        // Perform some operations on the asset here...

        $report = Report::generateAssetHistoryReport($asset->id); // Assuming this method exists
        $this->assertNotEmpty($report);
        $this->assertEquals($asset->id, $report->asset_id);
    }
    /**
     * Test asset merge report
     */
    public function test_assetMergeReport()
    {
        $asset1 = Asset::factory()->create(['name' => 'Asset 1']);
        $asset2 = Asset::factory()->create(['name' => 'Asset 2']);

        // Assuming you have a method to merge assets
        $mergedAsset = Asset::mergeAssets($asset1->id, $asset2->id);

        $report = Report::generateAssetMergeReport(); // Assuming this method exists
        
        $this->assertContains($mergedAsset->id, $report->merged_assets->pluck('id')->toArray());
    }

    /**
     * Test depreciation report
     */
    public function test_depreciationReport()
    {
        $asset = Asset::factory()->create(['cost' => 1000]);
        $depreciationRate = 10; // 10%

        // Assuming the depreciation process was called earlier
        $depreciation = new Depreciation();
        $depreciation->process($asset->id, $depreciationRate);

        $report = Report::generateDepreciationReport(); // Assuming this method exists
        
        $this->assertContains($asset->id, $report->assets->pluck('id')->toArray());
    }

    /**
     * Test maintenance ledger report
     */
    public function test_maintenanceLedgerReport()
    {
        $asset = Asset::factory()->create();
        $maintenanceData = [
            'description' => 'Annual check-up',
            'date' => '2024-06-01',
            'asset_id' => $asset->id
        ];

        MaintenanceRecord::create($maintenanceData);

        $report = Report::generateMaintenanceLedgerReport(); // Assuming this method exists
        
        $this->assertContains($maintenanceData['description'], $report->maintenance_records->pluck('description')->toArray());
    }

    /**
     * Test status-wise report
     */
    public function test_statusWiseReport()
    {
        $asset1 = Asset::factory()->create(['status' => 'active']);
        $asset2 = Asset::factory()->create(['status' => 'inactive']);

        $report = Report::generateStatusWiseReport(); // Assuming this method exists
        
        $this->assertCount(1, $report->active_assets);
        $this->assertCount(1, $report->inactive_assets);
    }
}
