<?php

use Tests\TestCase;
use App\Models\HolidayCalendar;
use App\Models\LeaveEntry;
use App\Models\HolidayEntry;
use App\Models\LeaveSummary;
use App\Models\LeaveApproval;
use App\Models\EmployeeLeaveApplication;
use App\Models\LeaveBalanceReport;

class LeaveManagementTest extends TestCase
{
    /**
     * Test holiday calendar setup
     */
    public function test_holidayCalendarSetup()
    {
        $holiday = new HolidayCalendar();
        $holiday->id = 1; // Replace with a valid ID if needed
        $result = false;
        try {
            $holiday->update(['date' => '2024-12-25', 'description' => 'Christmas Day']);
            $result = true;
        } catch (Exception $e) {
            // Handle exception
        }
        $this->assertTrue($result);
    }

    /**
     * Test leave entry creation
     */
    public function test_leaveEntryCreation()
    {
        $leaveData = [
            'employee_id' => 1, // Replace with a valid employee ID
            'leave_type' => 'Sick Leave',
            'start_date' => '2024-01-01',
            'end_date' => '2024-01-02',
            'status' => 'Pending',
        ];
        
        LeaveEntry::create($leaveData);
        $this->assertDatabaseHas('leave_entries', $leaveData);
    }

    /**
     * Test holiday entry creation
     */
    public function test_holidayEntryCreation()
    {
        $holidayData = [
            'date' => '2024-02-14', // Valentine's Day
            'description' => 'Valentine\'s Day',
        ];
        
        HolidayEntry::create($holidayData);
        $this->assertDatabaseHas('holiday_entries', $holidayData);
    }

    /**
     * Test leave summary generation
     */
    public function test_leaveSummaryGeneration()
    {
        $summary = LeaveSummary::generateForEmployee(1); // Replace with a valid employee ID
        $this->assertNotEmpty($summary);
    }

    /**
     * Test leave approval process
     */
    public function test_leaveApproval()
    {
        $leaveEntry = LeaveEntry::factory()->create(['status' => 'Pending']); // Create a leave entry
        $leaveApproval = new LeaveApproval();
        
        $result = $leaveApproval->approve($leaveEntry->id);
        
        $this->assertTrue($result);
        $this->assertEquals('Approved', $leaveEntry->fresh()->status);
    }

    /**
     * Test employee leave application
     */
    public function test_employeeLeaveApplication()
    {
        $applicationData = [
            'employee_id' => 1, // Replace with a valid employee ID
            'leave_type' => 'Annual Leave',
            'start_date' => '2024-03-01',
            'end_date' => '2024-03-10',
        ];
        
        $application = EmployeeLeaveApplication::create($applicationData);
        $this->assertDatabaseHas('employee_leave_applications', $applicationData);
    }

    /**
     * Test leave balance report generation
     */
    public function test_leaveBalanceReportGeneration()
    {
        $report = LeaveBalanceReport::generateForEmployee(1); // Replace with a valid employee ID
        $this->assertNotEmpty($report);
    }
}