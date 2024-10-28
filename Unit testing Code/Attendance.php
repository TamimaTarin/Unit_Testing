<?php

use Tests\TestCase;
class AttendanceTest extends TestCase
{
   
    public function test_attendance()
    {
        $group = new attendance();
        $group->id = 1; // Replace with a valid ID if needed
        $result = false;
        try {
            $group->update(['name' => '']);
            $result = true;
        } catch (Exception $e) {
            // Handle exception
        }
        $this->assertTrue($result);
    }
}