<?php
/**
 * Copyright (c) 2007-2009, Conduit Internet Technologies, Inc.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *  - Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *  - Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *  - Neither the name of Conduit Internet Technologies, Inc. nor the names of
 *    its contributors may be used to endorse or promote products derived from
 *    this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @copyright Copyright 2007-2009 Conduit Internet Technologies, Inc. (http://conduit-it.com)
 * @license New BSD (http://solr-php-client.googlecode.com/svn/trunk/COPYING)
 *
 * @package Apache
 * @subpackage Solr
 * @author Donovan Jimenez <djimenez@conduit-it.com>
 */

/**
 * Apache_Solr_Response Unit Test
 */
class Apache_Solr_ResponseTest extends PHPUnit_Framework_TestCase
{
	static private $_goodHeaders = array(
		'HTTP/1.0 200 OK'
	);

	static public $_goodBody; // set later in file with heredoc

	static private function _simulateGoodResponse()
	{
		// here we simulate when our file_get_content call is
		// successful and has returned us valid JSON body and
		// header array
		return new Apache_Solr_Response(self::$_goodBody, self::$_goodHeaders);
	}

	static private function _simulateBadResponse()
	{
		// here we simulate when our file_get_content calls fail
		// the response body is false and the header is an undefined variable
		return new Apache_Solr_Response(false, null);
	}

	public function testConstuctorWithValidBodyAndHeaders()
	{
		$fixture = self::_simulateGoodResponse();

		// check that we parsed the HTTP status correctly
		$this->assertEquals(200, $fixture->getHttpStatus());

		// check that we received the body correctly
		$this->assertEquals(self::$_goodBody, $fixture->getRawResponse());

		// check that our defaults are correct
		$this->assertEquals('UTF-8', $fixture->getEncoding());
		$this->assertEquals('text/plain', $fixture->getType());
	}

	public function testConstructorWithBadBodyAndHeaders()
	{
		$fixture = self::_simulateBadResponse();

		// check that our defaults are correct
		$this->assertEquals(0, $fixture->getHttpStatus());
		$this->assertEquals('UTF-8', $fixture->getEncoding());
		$this->assertEquals('text/plain', $fixture->getType());
	}

	public function testMagicGetWithValidBodyAndHeaders()
	{
		$fixture = self::_simulateGoodResponse();

		// test top level gets
		$this->assertType('stdClass', $fixture->responseHeader);
		$this->assertEquals(0, $fixture->responseHeader->status);
		$this->assertEquals(0, $fixture->responseHeader->QTime);

		$this->assertType('stdClass', $fixture->response);
		$this->assertEquals(36649, $fixture->response->numFound);

		$this->assertTrue(is_array($fixture->response->docs));
		$this->assertEquals(10, count($fixture->response->docs));
	}

	public function testMagicGetWithInvalidBodyAndHeaders()
	{
		$fixture = self::_simulateBadResponse();

		// test top level gets
		$this->assertNull($fixture->responseHeader);
		$this->assertNull($fixture->response);
	}
}

// set the good body response
Apache_Solr_ResponseTest::$_goodBody = <<<JSON
{
 "responseHeader":{
  "status":0,
  "QTime":0,
  "params":{
	"indent":"on",
	"start":"0",
	"q":"cit_client:pierce AND cit_instance:dev AND cit_domain:my_fleet",
	"wt":"json",
	"rows":"10",
	"version":"2.2"}},
 "response":{"numFound":36649,"start":0,"docs":[
	{
	 "guid":"pierce/dev/my_fleet/36384",
	 "cit_client":"pierce",
	 "cit_instance":"dev",
	 "cit_domain":"my_fleet",
	 "account_id_s":"9946 17942 17948",
	 "machine_id_s":"36384",
	 "job_number_s":"19440",
	 "item_number_s":"",
	 "unit_number_s":"3",
	 "number_of_units_s":"3",
	 "work_order_s":"07831134",
	 "actual_ship_date_s":"2007-09-25",
	 "warranty_start_date_s":"2007-09-25",
	 "drawing_number_s":"19440AD",
	 "body_sales_option_description_s":"Aerial, 75' HD Ladder/HAL, Tandem/Quint, Alum Body",
	 "chassis_sales_option_description_s":"Dash-2000 Chassis, Aerials/Tankers Tandem 48K",
	 "truck_vin_s":"4P1CD01H77A007691",
	 "truck_vin_partial_s":"7A007691",
	 "desc_cab_s":"Cab, Dash-2000, 67\\" w/10\\" Raised Roof",
	 "desc_engine_s":"S60, 470 hp, 1650 torq, w/Jake, P/N 1466820, Dash/Lance, 2006 Allocated",
	 "desc_front_axle_s":"1674997                       Axle, Front, Oshkosh TAK-4, Non Drive, 19,500 lb,",
	 "desc_front_tire_s":"Tires, Michelin, 385/65R22.50 18 ply XTE2, Hiway Rib",
	 "desc_pump_s":"Pump, 2000 CSU Single Stage,Waterous",
	 "desc_rear_axle_s":"RD22145NFLF921                Axle, Rear, Meritor RT44-145, 44,000 lb",
	 "desc_rear_tire_s":"Tires, (8) Michelin, 11R22.50 16 ply, XZE",
	 "desc_tank_s":"Tank, Water, 500 Gallon, Poly, PAL",
	 "desc_transmission_s":"Trans, Allison Gen IV 4000 EVS PR",
	 "desc_foam_system_s":"No Foam System Required",
	 "desc_aerial_s":"Aerial, 75' Heavy Duty Ladder",
	 "desc_generator_s":"Onan 10 kW Hydraulic w/ Electronic Control, Hotshift PTO",
	 "desc_compartment_door_s":"Doors, Roll-up, Robinson - Side Compt",
	 "pressure_governor_s":"",
	 "gross_vehicle_weight_rating_s":"",
	 "gross_weight_without_water_s":"49240",
	 "gross_weight_with_water_s":"",
	 "front_weight_rating_s":"",
	 "front_weight_without_water_s":"16560",
	 "front_weight_with_water_s":"17160",
	 "rear_weight_rating_s":"",
	 "rear_weight_without_water_s":"32680",
	 "rear_weight_with_water_s":"36380",
	 "seating_capacity_s":"5",
	 "body_primary_paint_color_s":"#70 RED",
	 "body_secondary_paint_color_s":"",
	 "aerial_paint_color_s":"#10 WHITE",
	 "engine_serial_number_s":"06R0934718",
	 "transmission_serial_number_s":"6610232387",
	 "transfer_case_serial_number_s":"N/A",
	 "generator_serial_number_s":"D070052998",
	 "alternator_serial_number_s":"10686",
	 "pto_serial_number_s":"",
	 "pump_serial_number_s":"129251",
	 "tank_serial_number_s":"D4UPFW0731070522",
	 "aerial_serial_number_s":"19440-03",
	 "front_tire_serial_number_1_s":"HAWBB5TX4406",
	 "front_tire_serial_number_2_s":"",
	 "rear_tire_serial_number_1_s":"B63TA8VX2407",
	 "rear_tire_serial_number_2_s":"B63TA8VX2507",
	 "rear_tire_serial_number_3_s":"",
	 "rear_tire_serial_number_4_s":"",
	 "rear_tire_serial_number_5_s":"",
	 "rear_tire_serial_number_6_s":"",
	 "rear_tire_serial_number_7_s":"",
	 "rear_tire_serial_number_8_s":"",
	 "trail_tire_serial_number_1_s":"",
	 "trail_tire_serial_number_2_s":"",
	 "trail_tire_serial_number_3_s":"",
	 "trail_tire_serial_number_4_s":"",
	 "side_roll_protection_serial_number_s":"See Service Bulletin #189",
	 "multiplex_serial_number_s":"",
	 "front_axle_1_serial_number_s":"N/A",
	 "front_axle_2_serial_number_s":"",
	 "rear_axle_1_serial_number_s":"NKA07022868",
	 "rear_axle_2_serial_number_s":"",
	 "salesman_name_s":"BROWN ROGER",
	 "dealer_number_s":"2216537",
	 "contract_admin_name_s":"MADER, GARY R",
	 "dealer_name_s":"CONRAD FIRE EQUIPMENT INC",
	 "customer_name_s":"WICHITA CITY OF",
	 "customer_number_s":"448596",
	 "address_s":"CITY OF WICHITA FIRE DEPARTMENT",
	 "city_s":"Wichita",
	 "state_s":"KS",
	 "zip_s":"67203",
	 "country_s":"US",
	 "chief_name_s":"KC LAWSON",
	 "manual_id_s":"",
	 "manual_revision_id_s":"",
	 "assembly_revision_id_s":"",
	 "assembly_function_group_id_s":"",
	 "cit_timestamp":"2008-02-08T19:06:11.833Z",
	 "front_axle_2_serial_number_t":[
	  ""],
	 "rear_tire_serial_number_8":[
	  ""],
	 "job_number":[
	  "19440"],
	 "rear_tire_serial_number_7":[
	  ""],
	 "chassis_sales_option_description":[
	  "Dash-2000 Chassis, Aerials/Tankers Tandem 48K"],
	 "desc_pump":[
	  "Pump, 2000 CSU Single Stage,Waterous"],
	 "job_number_t":[
	  "19440"],
	 "chief_name":[
	  "KC LAWSON"],
	 "front_axle_2_serial_number":[
	  ""],
	 "contract_admin_name":[
	  "MADER, GARY R"],
	 "trail_tire_serial_number_3":[
	  ""],
	 "trail_tire_serial_number_4":[
	  ""],
	 "trail_tire_serial_number_1":[
	  ""],
	 "city":[
	  "Wichita"],
	 "trail_tire_serial_number_2":[
	  ""],
	 "rear_weight_with_water_t":[
	  "36380"],
	 "manual_id_t":[
	  ""],
	 "truck_vin_partial":[
	  "7A007691"],
	 "desc_engine_t":[
	  "S60, 470 hp, 1650 torq, w/Jake, P/N 1466820, Dash/Lance, 2006 Allocated"],
	 "generator_serial_number":[
	  "D070052998"],
	 "number_of_units_t":[
	  "3"],
	 "rear_weight_without_water_t":[
	  "32680"],
	 "rear_tire_serial_number_1":[
	  "B63TA8VX2407"],
	 "rear_tire_serial_number_2":[
	  "B63TA8VX2507"],
	 "rear_tire_serial_number_3":[
	  ""],
	 "rear_tire_serial_number_1_t":[
	  "B63TA8VX2407"],
	 "aerial_paint_color_t":[
	  "#10 WHITE"],
	 "dealer_name":[
	  "CONRAD FIRE EQUIPMENT INC"],
	 "rear_tire_serial_number_4":[
	  ""],
	 "multiplex_serial_number_t":[
	  ""],
	 "rear_tire_serial_number_5":[
	  ""],
	 "rear_tire_serial_number_6":[
	  ""],
	 "desc_front_axle":[
	  "1674997                       Axle, Front, Oshkosh TAK-4, Non Drive, 19,500 lb,"],
	 "unit_number":[
	  "3"],
	 "truck_vin_partial_t":[
	  "7A007691"],
	 "tank_serial_number_t":[
	  "D4UPFW0731070522"],
	 "gross_vehicle_weight_rating":[
	  ""],
	 "desc_foam_system":[
	  "No Foam System Required"],
	 "rear_tire_serial_number_3_t":[
	  ""],
	 "gross_weight_without_water_t":[
	  "49240"],
	 "salesman_name":[
	  "BROWN ROGER"],
	 "rear_tire_serial_number_5_t":[
	  ""],
	 "chassis_sales_option_description_t":[
	  "Dash-2000 Chassis, Aerials/Tankers Tandem 48K"],
	 "desc_engine":[
	  "S60, 470 hp, 1650 torq, w/Jake, P/N 1466820, Dash/Lance, 2006 Allocated"],
	 "pto_serial_number":[
	  ""],
	 "item_number_t":[
	  ""],
	 "gross_weight_with_water_t":[
	  ""],
	 "seating_capacity":[
	  "5"],
	 "front_tire_serial_number_2_t":[
	  ""],
	 "pump_serial_number":[
	  "129251"],
	 "pressure_governor_t":[
	  ""],
	 "desc_front_tire":[
	  "Tires, Michelin, 385/65R22.50 18 ply XTE2, Hiway Rib"],
	 "account_id_t":[
	  "9946 17942 17948"],
	 "number_of_units":[
	  "3"],
	 "state_t":[
	  "KS"],
	 "desc_foam_system_t":[
	  "No Foam System Required"],
	 "rear_axle_2_serial_number_t":[
	  ""],
	 "machine_id":[36384],
	 "rear_weight_without_water":[
	  "32680"],
	 "desc_rear_axle_t":[
	  "RD22145NFLF921                Axle, Rear, Meritor RT44-145, 44,000 lb"],
	 "body_primary_paint_color_t":[
	  "#70 RED"],
	 "address_t":[
	  "CITY OF WICHITA FIRE DEPARTMENT"],
	 "rear_axle_2_serial_number":[
	  ""],
	 "aerial_serial_number_t":[
	  "19440-03"],
	 "rear_tire_serial_number_2_t":[
	  "B63TA8VX2507"],
	 "front_tire_serial_number_1":[
	  "HAWBB5TX4406"],
	 "drawing_number_t":[
	  "19440AD"],
	 "front_weight_rating_t":[
	  ""],
	 "front_tire_serial_number_2":[
	  ""],
	 "dealer_name_t":[
	  "CONRAD FIRE EQUIPMENT INC"],
	 "salesman_name_t":[
	  "BROWN ROGER"],
	 "truck_vin_t":[
	  "4P1CD01H77A007691"],
	 "gross_weight_with_water":[
	  ""],
	 "desc_transmission":[
	  "Trans, Allison Gen IV 4000 EVS PR"],
	 "desc_tank":[
	  "Tank, Water, 500 Gallon, Poly, PAL"],
	 "desc_compartment_door":[
	  "Doors, Roll-up, Robinson - Side Compt"],
	 "transmission_serial_number_t":[
	  "6610232387"],
	 "dealer_number":[
	  "2216537"],
	 "unit_number_t":[
	  "3"],
	 "chief_name_t":[
	  "KC LAWSON"],
	 "aerial_serial_number":[
	  "19440-03"],
	 "front_weight_with_water_t":[
	  "17160"],
	 "generator_serial_number_t":[
	  "D070052998"],
	 "seating_capacity_t":[
	  "5"],
	 "side_roll_protection_serial_number":[
	  "See Service Bulletin #189"],
	 "desc_tank_t":[
	  "Tank, Water, 500 Gallon, Poly, PAL"],
	 "actual_ship_date_t":[
	  "2007-09-25"],
	 "pump_serial_number_t":[
	  "129251"],
	 "desc_cab":[
	  "Cab, Dash-2000, 67\\" w/10\\" Raised Roof"],
	 "engine_serial_number":[
	  "06R0934718"],
	 "trail_tire_serial_number_4_t":[
	  ""],
	 "manual_revision_id_t":[
	  ""],
	 "rear_weight_rating_t":[
	  ""],
	 "rear_axle_1_serial_number":[
	  "NKA07022868"],
	 "desc_rear_axle":[
	  "RD22145NFLF921                Axle, Rear, Meritor RT44-145, 44,000 lb"],
	 "body_secondary_paint_color":[
	  ""],
	 "truck_vin":[
	  "4P1CD01H77A007691"],
	 "front_axle_1_serial_number_t":[
	  "N/A"],
	 "trail_tire_serial_number_2_t":[
	  ""],
	 "desc_cab_t":[
	  "Cab, Dash-2000, 67\\" w/10\\" Raised Roof"],
	 "dealer_number_t":[
	  "2216537"],
	 "trail_tire_serial_number_1_t":[
	  ""],
	 "city_t":[
	  "Wichita"],
	 "front_weight_without_water_t":[
	  "16560"],
	 "desc_rear_tire_t":[
	  "Tires, (8) Michelin, 11R22.50 16 ply, XZE"],
	 "account_id":[17942,9946,17948],
	 "side_roll_protection_serial_number_t":[
	  "See Service Bulletin #189"],
	 "actual_ship_date":[
	  "2007-09-25"],
	 "alternator_serial_number":[
	  "10686"],
	 "customer_name":[
	  "WICHITA CITY OF"],
	 "transfer_case_serial_number":[
	  "N/A"],
	 "desc_generator_t":[
	  "Onan 10 kW Hydraulic w/ Electronic Control, Hotshift PTO"],
	 "engine_serial_number_t":[
	  "06R0934718"],
	 "alternator_serial_number_t":[
	  "10686"],
	 "country":[
	  "US"],
	 "rear_tire_serial_number_4_t":[
	  ""],
	 "desc_front_tire_t":[
	  "Tires, Michelin, 385/65R22.50 18 ply XTE2, Hiway Rib"],
	 "body_secondary_paint_color_t":[
	  ""],
	 "desc_rear_tire":[
	  "Tires, (8) Michelin, 11R22.50 16 ply, XZE"],
	 "pto_serial_number_t":[
	  ""],
	 "trail_tire_serial_number_3_t":[
	  ""],
	 "desc_compartment_door_t":[
	  "Doors, Roll-up, Robinson - Side Compt"],
	 "desc_pump_t":[
	  "Pump, 2000 CSU Single Stage,Waterous"],
	 "gross_weight_without_water":[
	  "49240"],
	 "assembly_function_group_id_t":[
	  ""],
	 "desc_aerial_t":[
	  "Aerial, 75' Heavy Duty Ladder"],
	 "drawing_number":[
	  "19440AD"],
	 "item_number":[
	  ""],
	 "rear_weight_with_water":[
	  "36380"],
	 "rear_tire_serial_number_7_t":[
	  ""],
	 "work_order_t":[
	  "07831134"],
	 "state":[
	  "KS"],
	 "work_order":[
	  "07831134"],
	 "customer_name_t":[
	  "WICHITA CITY OF"],
	 "rear_tire_serial_number_6_t":[
	  ""],
	 "tank_serial_number":[
	  "D4UPFW0731070522"],
	 "zip_t":[
	  "67203"],
	 "customer_number":[
	  "448596"],
	 "body_primary_paint_color":[
	  "#70 RED"],
	 "rear_axle_1_serial_number_t":[
	  "NKA07022868"],
	 "body_sales_option_description":[
	  "Aerial, 75' HD Ladder/HAL, Tandem/Quint, Alum Body"],
	 "aerial_paint_color":[
	  "#10 WHITE"],
	 "desc_generator":[
	  "Onan 10 kW Hydraulic w/ Electronic Control, Hotshift PTO"],
	 "body_sales_option_description_t":[
	  "Aerial, 75' HD Ladder/HAL, Tandem/Quint, Alum Body"],
	 "desc_aerial":[
	  "Aerial, 75' Heavy Duty Ladder"],
	 "warranty_start_date":[
	  "2007-09-25"],
	 "transfer_case_serial_number_t":[
	  "N/A"],
	 "zip":[
	  "67203"],
	 "transmission_serial_number":[
	  "6610232387"],
	 "machine_id_t":[
	  "36384"],
	 "gross_vehicle_weight_rating_t":[
	  ""],
	 "country_t":[
	  "US"],
	 "desc_transmission_t":[
	  "Trans, Allison Gen IV 4000 EVS PR"],
	 "pressure_governor":[
	  ""],
	 "front_weight_rating":[
	  ""],
	 "customer_number_t":[
	  "448596"],
	 "front_weight_without_water":[
	  "16560"],
	 "contract_admin_name_t":[
	  "MADER, GARY R"],
	 "front_axle_1_serial_number":[
	  "N/A"],
	 "rear_tire_serial_number_8_t":[
	  ""],
	 "address":[
	  "CITY OF WICHITA FIRE DEPARTMENT"],
	 "desc_front_axle_t":[
	  "1674997                       Axle, Front, Oshkosh TAK-4, Non Drive, 19,500 lb,"],
	 "assembly_revision_id_t":[
	  ""],
	 "front_tire_serial_number_1_t":[
	  "HAWBB5TX4406"],
	 "multiplex_serial_number":[
	  ""],
	 "front_weight_with_water":[
	  "17160"],
	 "warranty_start_date_t":[
	  "2007-09-25"],
	 "rear_weight_rating":[
	  ""]},
	{
	 "guid":"pierce/dev/my_fleet/2",
	 "cit_client":"pierce",
	 "cit_instance":"dev",
	 "cit_domain":"my_fleet",
	 "account_id_s":"1 3 9946",
	 "machine_id_s":"2",
	 "job_number_s":"FC0006",
	 "item_number_s":"FC0006",
	 "unit_number_s":"1",
	 "number_of_units_s":"1",
	 "work_order_s":"",
	 "actual_ship_date_s":"1999-10-31",
	 "warranty_start_date_s":"1999-06-28",
	 "drawing_number_s":"",
	 "body_sales_option_description_s":"",
	 "chassis_sales_option_description_s":"",
	 "truck_vin_s":"",
	 "truck_vin_partial_s":"",
	 "desc_cab_s":"",
	 "desc_engine_s":"",
	 "desc_front_axle_s":"",
	 "desc_front_tire_s":"",
	 "desc_pump_s":"",
	 "desc_rear_axle_s":"",
	 "desc_rear_tire_s":"",
	 "desc_tank_s":"",
	 "desc_transmission_s":"",
	 "desc_foam_system_s":"",
	 "desc_aerial_s":"",
	 "desc_generator_s":"",
	 "desc_compartment_door_s":"",
	 "pressure_governor_s":"",
	 "gross_vehicle_weight_rating_s":"",
	 "gross_weight_without_water_s":"",
	 "gross_weight_with_water_s":"",
	 "front_weight_rating_s":"",
	 "front_weight_without_water_s":"",
	 "front_weight_with_water_s":"",
	 "rear_weight_rating_s":"",
	 "rear_weight_without_water_s":"",
	 "rear_weight_with_water_s":"",
	 "seating_capacity_s":"",
	 "body_primary_paint_color_s":"",
	 "body_secondary_paint_color_s":"",
	 "aerial_paint_color_s":"",
	 "engine_serial_number_s":"",
	 "transmission_serial_number_s":"",
	 "transfer_case_serial_number_s":"",
	 "generator_serial_number_s":"",
	 "alternator_serial_number_s":"",
	 "pto_serial_number_s":"",
	 "pump_serial_number_s":"",
	 "tank_serial_number_s":"",
	 "aerial_serial_number_s":"",
	 "front_tire_serial_number_1_s":"",
	 "front_tire_serial_number_2_s":"",
	 "rear_tire_serial_number_1_s":"",
	 "rear_tire_serial_number_2_s":"",
	 "rear_tire_serial_number_3_s":"",
	 "rear_tire_serial_number_4_s":"",
	 "rear_tire_serial_number_5_s":"",
	 "rear_tire_serial_number_6_s":"",
	 "rear_tire_serial_number_7_s":"",
	 "rear_tire_serial_number_8_s":"",
	 "trail_tire_serial_number_1_s":"",
	 "trail_tire_serial_number_2_s":"",
	 "trail_tire_serial_number_3_s":"",
	 "trail_tire_serial_number_4_s":"",
	 "side_roll_protection_serial_number_s":"See Service Bulletin #189",
	 "multiplex_serial_number_s":"",
	 "front_axle_1_serial_number_s":"",
	 "front_axle_2_serial_number_s":"",
	 "rear_axle_1_serial_number_s":"",
	 "rear_axle_2_serial_number_s":"",
	 "salesman_name_s":"",
	 "dealer_number_s":"",
	 "contract_admin_name_s":"",
	 "dealer_name_s":"",
	 "customer_name_s":"GARDEN GROVE FIRE DEPARTMENT",
	 "customer_number_s":"3785",
	 "address_s":"13802 NEW HOPE STREET",
	 "city_s":"Garden Grove",
	 "state_s":"CA",
	 "zip_s":"92843",
	 "country_s":"US",
	 "chief_name_s":"",
	 "manual_id_s":"",
	 "manual_revision_id_s":"",
	 "assembly_revision_id_s":"",
	 "assembly_function_group_id_s":"",
	 "cit_timestamp":"2008-02-08T19:06:11.860Z",
	 "front_axle_2_serial_number_t":[
	  ""],
	 "rear_tire_serial_number_8":[
	  ""],
	 "job_number":[
	  "FC0006"],
	 "rear_tire_serial_number_7":[
	  ""],
	 "chassis_sales_option_description":[
	  ""],
	 "desc_pump":[
	  ""],
	 "job_number_t":[
	  "FC0006"],
	 "chief_name":[
	  ""],
	 "front_axle_2_serial_number":[
	  ""],
	 "contract_admin_name":[
	  ""],
	 "trail_tire_serial_number_3":[
	  ""],
	 "trail_tire_serial_number_4":[
	  ""],
	 "trail_tire_serial_number_1":[
	  ""],
	 "city":[
	  "Garden Grove"],
	 "trail_tire_serial_number_2":[
	  ""],
	 "rear_weight_with_water_t":[
	  ""],
	 "manual_id_t":[
	  ""],
	 "truck_vin_partial":[
	  ""],
	 "desc_engine_t":[
	  ""],
	 "generator_serial_number":[
	  ""],
	 "number_of_units_t":[
	  "1"],
	 "rear_weight_without_water_t":[
	  ""],
	 "rear_tire_serial_number_1":[
	  ""],
	 "rear_tire_serial_number_2":[
	  ""],
	 "rear_tire_serial_number_3":[
	  ""],
	 "rear_tire_serial_number_1_t":[
	  ""],
	 "aerial_paint_color_t":[
	  ""],
	 "dealer_name":[
	  ""],
	 "rear_tire_serial_number_4":[
	  ""],
	 "multiplex_serial_number_t":[
	  ""],
	 "rear_tire_serial_number_5":[
	  ""],
	 "rear_tire_serial_number_6":[
	  ""],
	 "desc_front_axle":[
	  ""],
	 "unit_number":[
	  "1"],
	 "truck_vin_partial_t":[
	  ""],
	 "tank_serial_number_t":[
	  ""],
	 "gross_vehicle_weight_rating":[
	  ""],
	 "desc_foam_system":[
	  ""],
	 "rear_tire_serial_number_3_t":[
	  ""],
	 "gross_weight_without_water_t":[
	  ""],
	 "salesman_name":[
	  ""],
	 "rear_tire_serial_number_5_t":[
	  ""],
	 "chassis_sales_option_description_t":[
	  ""],
	 "desc_engine":[
	  ""],
	 "pto_serial_number":[
	  ""],
	 "item_number_t":[
	  "FC0006"],
	 "gross_weight_with_water_t":[
	  ""],
	 "seating_capacity":[
	  ""],
	 "front_tire_serial_number_2_t":[
	  ""],
	 "pump_serial_number":[
	  ""],
	 "pressure_governor_t":[
	  ""],
	 "desc_front_tire":[
	  ""],
	 "account_id_t":[
	  "1 3 9946"],
	 "number_of_units":[
	  "1"],
	 "state_t":[
	  "CA"],
	 "desc_foam_system_t":[
	  ""],
	 "rear_axle_2_serial_number_t":[
	  ""],
	 "machine_id":[2],
	 "rear_weight_without_water":[
	  ""],
	 "desc_rear_axle_t":[
	  ""],
	 "body_primary_paint_color_t":[
	  ""],
	 "address_t":[
	  "13802 NEW HOPE STREET"],
	 "rear_axle_2_serial_number":[
	  ""],
	 "aerial_serial_number_t":[
	  ""],
	 "rear_tire_serial_number_2_t":[
	  ""],
	 "front_tire_serial_number_1":[
	  ""],
	 "drawing_number_t":[
	  ""],
	 "front_weight_rating_t":[
	  ""],
	 "front_tire_serial_number_2":[
	  ""],
	 "dealer_name_t":[
	  ""],
	 "salesman_name_t":[
	  ""],
	 "truck_vin_t":[
	  ""],
	 "gross_weight_with_water":[
	  ""],
	 "desc_transmission":[
	  ""],
	 "desc_tank":[
	  ""],
	 "desc_compartment_door":[
	  ""],
	 "transmission_serial_number_t":[
	  ""],
	 "dealer_number":[
	  ""],
	 "unit_number_t":[
	  "1"],
	 "chief_name_t":[
	  ""],
	 "aerial_serial_number":[
	  ""],
	 "front_weight_with_water_t":[
	  ""],
	 "generator_serial_number_t":[
	  ""],
	 "seating_capacity_t":[
	  ""],
	 "side_roll_protection_serial_number":[
	  "See Service Bulletin #189"],
	 "desc_tank_t":[
	  ""],
	 "actual_ship_date_t":[
	  "1999-10-31"],
	 "pump_serial_number_t":[
	  ""],
	 "desc_cab":[
	  ""],
	 "engine_serial_number":[
	  ""],
	 "trail_tire_serial_number_4_t":[
	  ""],
	 "manual_revision_id_t":[
	  ""],
	 "rear_weight_rating_t":[
	  ""],
	 "rear_axle_1_serial_number":[
	  ""],
	 "desc_rear_axle":[
	  ""],
	 "body_secondary_paint_color":[
	  ""],
	 "truck_vin":[
	  ""],
	 "front_axle_1_serial_number_t":[
	  ""],
	 "trail_tire_serial_number_2_t":[
	  ""],
	 "desc_cab_t":[
	  ""],
	 "dealer_number_t":[
	  ""],
	 "trail_tire_serial_number_1_t":[
	  ""],
	 "city_t":[
	  "Garden Grove"],
	 "front_weight_without_water_t":[
	  ""],
	 "desc_rear_tire_t":[
	  ""],
	 "account_id":[1,3,9946],
	 "side_roll_protection_serial_number_t":[
	  "See Service Bulletin #189"],
	 "actual_ship_date":[
	  "1999-10-31"],
	 "alternator_serial_number":[
	  ""],
	 "customer_name":[
	  "GARDEN GROVE FIRE DEPARTMENT"],
	 "transfer_case_serial_number":[
	  ""],
	 "desc_generator_t":[
	  ""],
	 "engine_serial_number_t":[
	  ""],
	 "alternator_serial_number_t":[
	  ""],
	 "country":[
	  "US"],
	 "rear_tire_serial_number_4_t":[
	  ""],
	 "desc_front_tire_t":[
	  ""],
	 "body_secondary_paint_color_t":[
	  ""],
	 "desc_rear_tire":[
	  ""],
	 "pto_serial_number_t":[
	  ""],
	 "trail_tire_serial_number_3_t":[
	  ""],
	 "desc_compartment_door_t":[
	  ""],
	 "desc_pump_t":[
	  ""],
	 "gross_weight_without_water":[
	  ""],
	 "assembly_function_group_id_t":[
	  ""],
	 "desc_aerial_t":[
	  ""],
	 "drawing_number":[
	  ""],
	 "item_number":[
	  "FC0006"],
	 "rear_weight_with_water":[
	  ""],
	 "rear_tire_serial_number_7_t":[
	  ""],
	 "work_order_t":[
	  ""],
	 "state":[
	  "CA"],
	 "work_order":[
	  ""],
	 "customer_name_t":[
	  "GARDEN GROVE FIRE DEPARTMENT"],
	 "rear_tire_serial_number_6_t":[
	  ""],
	 "tank_serial_number":[
	  ""],
	 "zip_t":[
	  "92843"],
	 "customer_number":[
	  "3785"],
	 "body_primary_paint_color":[
	  ""],
	 "rear_axle_1_serial_number_t":[
	  ""],
	 "body_sales_option_description":[
	  ""],
	 "aerial_paint_color":[
	  ""],
	 "desc_generator":[
	  ""],
	 "body_sales_option_description_t":[
	  ""],
	 "desc_aerial":[
	  ""],
	 "warranty_start_date":[
	  "1999-06-28"],
	 "transfer_case_serial_number_t":[
	  ""],
	 "zip":[
	  "92843"],
	 "transmission_serial_number":[
	  ""],
	 "machine_id_t":[
	  "2"],
	 "gross_vehicle_weight_rating_t":[
	  ""],
	 "country_t":[
	  "US"],
	 "desc_transmission_t":[
	  ""],
	 "pressure_governor":[
	  ""],
	 "front_weight_rating":[
	  ""],
	 "customer_number_t":[
	  "3785"],
	 "front_weight_without_water":[
	  ""],
	 "contract_admin_name_t":[
	  ""],
	 "front_axle_1_serial_number":[
	  ""],
	 "rear_tire_serial_number_8_t":[
	  ""],
	 "address":[
	  "13802 NEW HOPE STREET"],
	 "desc_front_axle_t":[
	  ""],
	 "assembly_revision_id_t":[
	  ""],
	 "front_tire_serial_number_1_t":[
	  ""],
	 "multiplex_serial_number":[
	  ""],
	 "front_weight_with_water":[
	  ""],
	 "warranty_start_date_t":[
	  "1999-06-28"],
	 "rear_weight_rating":[
	  ""]},
	{
	 "guid":"pierce/dev/my_fleet/3",
	 "cit_client":"pierce",
	 "cit_instance":"dev",
	 "cit_domain":"my_fleet",
	 "account_id_s":"1 3 9946",
	 "machine_id_s":"3",
	 "job_number_s":"FC0007",
	 "item_number_s":"FC0007",
	 "unit_number_s":"1",
	 "number_of_units_s":"1",
	 "work_order_s":"",
	 "actual_ship_date_s":"1999-10-31",
	 "warranty_start_date_s":"1999-06-28",
	 "drawing_number_s":"",
	 "body_sales_option_description_s":"",
	 "chassis_sales_option_description_s":"",
	 "truck_vin_s":"",
	 "truck_vin_partial_s":"",
	 "desc_cab_s":"",
	 "desc_engine_s":"",
	 "desc_front_axle_s":"",
	 "desc_front_tire_s":"",
	 "desc_pump_s":"",
	 "desc_rear_axle_s":"",
	 "desc_rear_tire_s":"",
	 "desc_tank_s":"",
	 "desc_transmission_s":"",
	 "desc_foam_system_s":"",
	 "desc_aerial_s":"",
	 "desc_generator_s":"",
	 "desc_compartment_door_s":"",
	 "pressure_governor_s":"",
	 "gross_vehicle_weight_rating_s":"",
	 "gross_weight_without_water_s":"",
	 "gross_weight_with_water_s":"",
	 "front_weight_rating_s":"",
	 "front_weight_without_water_s":"",
	 "front_weight_with_water_s":"",
	 "rear_weight_rating_s":"",
	 "rear_weight_without_water_s":"",
	 "rear_weight_with_water_s":"",
	 "seating_capacity_s":"",
	 "body_primary_paint_color_s":"",
	 "body_secondary_paint_color_s":"",
	 "aerial_paint_color_s":"",
	 "engine_serial_number_s":"",
	 "transmission_serial_number_s":"",
	 "transfer_case_serial_number_s":"",
	 "generator_serial_number_s":"",
	 "alternator_serial_number_s":"",
	 "pto_serial_number_s":"",
	 "pump_serial_number_s":"",
	 "tank_serial_number_s":"",
	 "aerial_serial_number_s":"",
	 "front_tire_serial_number_1_s":"",
	 "front_tire_serial_number_2_s":"",
	 "rear_tire_serial_number_1_s":"",
	 "rear_tire_serial_number_2_s":"",
	 "rear_tire_serial_number_3_s":"",
	 "rear_tire_serial_number_4_s":"",
	 "rear_tire_serial_number_5_s":"",
	 "rear_tire_serial_number_6_s":"",
	 "rear_tire_serial_number_7_s":"",
	 "rear_tire_serial_number_8_s":"",
	 "trail_tire_serial_number_1_s":"",
	 "trail_tire_serial_number_2_s":"",
	 "trail_tire_serial_number_3_s":"",
	 "trail_tire_serial_number_4_s":"",
	 "side_roll_protection_serial_number_s":"See Service Bulletin #189",
	 "multiplex_serial_number_s":"",
	 "front_axle_1_serial_number_s":"",
	 "front_axle_2_serial_number_s":"",
	 "rear_axle_1_serial_number_s":"",
	 "rear_axle_2_serial_number_s":"",
	 "salesman_name_s":"",
	 "dealer_number_s":"",
	 "contract_admin_name_s":"",
	 "dealer_name_s":"",
	 "customer_name_s":"EL SEGUNDO CITY OF",
	 "customer_number_s":"301673",
	 "address_s":"314 MAIN ST",
	 "city_s":"El Segundo",
	 "state_s":"CA",
	 "zip_s":"90245",
	 "country_s":"US",
	 "chief_name_s":"ROD PABST",
	 "manual_id_s":"",
	 "manual_revision_id_s":"",
	 "assembly_revision_id_s":"",
	 "assembly_function_group_id_s":"",
	 "cit_timestamp":"2008-02-08T19:06:11.913Z",
	 "front_axle_2_serial_number_t":[
	  ""],
	 "rear_tire_serial_number_8":[
	  ""],
	 "job_number":[
	  "FC0007"],
	 "rear_tire_serial_number_7":[
	  ""],
	 "chassis_sales_option_description":[
	  ""],
	 "desc_pump":[
	  ""],
	 "job_number_t":[
	  "FC0007"],
	 "chief_name":[
	  "ROD PABST"],
	 "front_axle_2_serial_number":[
	  ""],
	 "contract_admin_name":[
	  ""],
	 "trail_tire_serial_number_3":[
	  ""],
	 "trail_tire_serial_number_4":[
	  ""],
	 "trail_tire_serial_number_1":[
	  ""],
	 "city":[
	  "El Segundo"],
	 "trail_tire_serial_number_2":[
	  ""],
	 "rear_weight_with_water_t":[
	  ""],
	 "manual_id_t":[
	  ""],
	 "truck_vin_partial":[
	  ""],
	 "desc_engine_t":[
	  ""],
	 "generator_serial_number":[
	  ""],
	 "number_of_units_t":[
	  "1"],
	 "rear_weight_without_water_t":[
	  ""],
	 "rear_tire_serial_number_1":[
	  ""],
	 "rear_tire_serial_number_2":[
	  ""],
	 "rear_tire_serial_number_3":[
	  ""],
	 "rear_tire_serial_number_1_t":[
	  ""],
	 "aerial_paint_color_t":[
	  ""],
	 "dealer_name":[
	  ""],
	 "rear_tire_serial_number_4":[
	  ""],
	 "multiplex_serial_number_t":[
	  ""],
	 "rear_tire_serial_number_5":[
	  ""],
	 "rear_tire_serial_number_6":[
	  ""],
	 "desc_front_axle":[
	  ""],
	 "unit_number":[
	  "1"],
	 "truck_vin_partial_t":[
	  ""],
	 "tank_serial_number_t":[
	  ""],
	 "gross_vehicle_weight_rating":[
	  ""],
	 "desc_foam_system":[
	  ""],
	 "rear_tire_serial_number_3_t":[
	  ""],
	 "gross_weight_without_water_t":[
	  ""],
	 "salesman_name":[
	  ""],
	 "rear_tire_serial_number_5_t":[
	  ""],
	 "chassis_sales_option_description_t":[
	  ""],
	 "desc_engine":[
	  ""],
	 "pto_serial_number":[
	  ""],
	 "item_number_t":[
	  "FC0007"],
	 "gross_weight_with_water_t":[
	  ""],
	 "seating_capacity":[
	  ""],
	 "front_tire_serial_number_2_t":[
	  ""],
	 "pump_serial_number":[
	  ""],
	 "pressure_governor_t":[
	  ""],
	 "desc_front_tire":[
	  ""],
	 "account_id_t":[
	  "1 3 9946"],
	 "number_of_units":[
	  "1"],
	 "state_t":[
	  "CA"],
	 "desc_foam_system_t":[
	  ""],
	 "rear_axle_2_serial_number_t":[
	  ""],
	 "machine_id":[3],
	 "rear_weight_without_water":[
	  ""],
	 "desc_rear_axle_t":[
	  ""],
	 "body_primary_paint_color_t":[
	  ""],
	 "address_t":[
	  "314 MAIN ST"],
	 "rear_axle_2_serial_number":[
	  ""],
	 "aerial_serial_number_t":[
	  ""],
	 "rear_tire_serial_number_2_t":[
	  ""],
	 "front_tire_serial_number_1":[
	  ""],
	 "drawing_number_t":[
	  ""],
	 "front_weight_rating_t":[
	  ""],
	 "front_tire_serial_number_2":[
	  ""],
	 "dealer_name_t":[
	  ""],
	 "salesman_name_t":[
	  ""],
	 "truck_vin_t":[
	  ""],
	 "gross_weight_with_water":[
	  ""],
	 "desc_transmission":[
	  ""],
	 "desc_tank":[
	  ""],
	 "desc_compartment_door":[
	  ""],
	 "transmission_serial_number_t":[
	  ""],
	 "dealer_number":[
	  ""],
	 "unit_number_t":[
	  "1"],
	 "chief_name_t":[
	  "ROD PABST"],
	 "aerial_serial_number":[
	  ""],
	 "front_weight_with_water_t":[
	  ""],
	 "generator_serial_number_t":[
	  ""],
	 "seating_capacity_t":[
	  ""],
	 "side_roll_protection_serial_number":[
	  "See Service Bulletin #189"],
	 "desc_tank_t":[
	  ""],
	 "actual_ship_date_t":[
	  "1999-10-31"],
	 "pump_serial_number_t":[
	  ""],
	 "desc_cab":[
	  ""],
	 "engine_serial_number":[
	  ""],
	 "trail_tire_serial_number_4_t":[
	  ""],
	 "manual_revision_id_t":[
	  ""],
	 "rear_weight_rating_t":[
	  ""],
	 "rear_axle_1_serial_number":[
	  ""],
	 "desc_rear_axle":[
	  ""],
	 "body_secondary_paint_color":[
	  ""],
	 "truck_vin":[
	  ""],
	 "front_axle_1_serial_number_t":[
	  ""],
	 "trail_tire_serial_number_2_t":[
	  ""],
	 "desc_cab_t":[
	  ""],
	 "dealer_number_t":[
	  ""],
	 "trail_tire_serial_number_1_t":[
	  ""],
	 "city_t":[
	  "El Segundo"],
	 "front_weight_without_water_t":[
	  ""],
	 "desc_rear_tire_t":[
	  ""],
	 "account_id":[1,3,9946],
	 "side_roll_protection_serial_number_t":[
	  "See Service Bulletin #189"],
	 "actual_ship_date":[
	  "1999-10-31"],
	 "alternator_serial_number":[
	  ""],
	 "customer_name":[
	  "EL SEGUNDO CITY OF"],
	 "transfer_case_serial_number":[
	  ""],
	 "desc_generator_t":[
	  ""],
	 "engine_serial_number_t":[
	  ""],
	 "alternator_serial_number_t":[
	  ""],
	 "country":[
	  "US"],
	 "rear_tire_serial_number_4_t":[
	  ""],
	 "desc_front_tire_t":[
	  ""],
	 "body_secondary_paint_color_t":[
	  ""],
	 "desc_rear_tire":[
	  ""],
	 "pto_serial_number_t":[
	  ""],
	 "trail_tire_serial_number_3_t":[
	  ""],
	 "desc_compartment_door_t":[
	  ""],
	 "desc_pump_t":[
	  ""],
	 "gross_weight_without_water":[
	  ""],
	 "assembly_function_group_id_t":[
	  ""],
	 "desc_aerial_t":[
	  ""],
	 "drawing_number":[
	  ""],
	 "item_number":[
	  "FC0007"],
	 "rear_weight_with_water":[
	  ""],
	 "rear_tire_serial_number_7_t":[
	  ""],
	 "work_order_t":[
	  ""],
	 "state":[
	  "CA"],
	 "work_order":[
	  ""],
	 "customer_name_t":[
	  "EL SEGUNDO CITY OF"],
	 "rear_tire_serial_number_6_t":[
	  ""],
	 "tank_serial_number":[
	  ""],
	 "zip_t":[
	  "90245"],
	 "customer_number":[
	  "301673"],
	 "body_primary_paint_color":[
	  ""],
	 "rear_axle_1_serial_number_t":[
	  ""],
	 "body_sales_option_description":[
	  ""],
	 "aerial_paint_color":[
	  ""],
	 "desc_generator":[
	  ""],
	 "body_sales_option_description_t":[
	  ""],
	 "desc_aerial":[
	  ""],
	 "warranty_start_date":[
	  "1999-06-28"],
	 "transfer_case_serial_number_t":[
	  ""],
	 "zip":[
	  "90245"],
	 "transmission_serial_number":[
	  ""],
	 "machine_id_t":[
	  "3"],
	 "gross_vehicle_weight_rating_t":[
	  ""],
	 "country_t":[
	  "US"],
	 "desc_transmission_t":[
	  ""],
	 "pressure_governor":[
	  ""],
	 "front_weight_rating":[
	  ""],
	 "customer_number_t":[
	  "301673"],
	 "front_weight_without_water":[
	  ""],
	 "contract_admin_name_t":[
	  ""],
	 "front_axle_1_serial_number":[
	  ""],
	 "rear_tire_serial_number_8_t":[
	  ""],
	 "address":[
	  "314 MAIN ST"],
	 "desc_front_axle_t":[
	  ""],
	 "assembly_revision_id_t":[
	  ""],
	 "front_tire_serial_number_1_t":[
	  ""],
	 "multiplex_serial_number":[
	  ""],
	 "front_weight_with_water":[
	  ""],
	 "warranty_start_date_t":[
	  "1999-06-28"],
	 "rear_weight_rating":[
	  ""]},
	{
	 "guid":"pierce/dev/my_fleet/4",
	 "cit_client":"pierce",
	 "cit_instance":"dev",
	 "cit_domain":"my_fleet",
	 "account_id_s":"1 3 9946",
	 "machine_id_s":"4",
	 "job_number_s":"FC0009",
	 "item_number_s":"FC0009",
	 "unit_number_s":"1",
	 "number_of_units_s":"1",
	 "work_order_s":"",
	 "actual_ship_date_s":"1999-10-31",
	 "warranty_start_date_s":"1999-06-28",
	 "drawing_number_s":"",
	 "body_sales_option_description_s":"",
	 "chassis_sales_option_description_s":"",
	 "truck_vin_s":"",
	 "truck_vin_partial_s":"",
	 "desc_cab_s":"",
	 "desc_engine_s":"",
	 "desc_front_axle_s":"",
	 "desc_front_tire_s":"",
	 "desc_pump_s":"",
	 "desc_rear_axle_s":"",
	 "desc_rear_tire_s":"",
	 "desc_tank_s":"",
	 "desc_transmission_s":"",
	 "desc_foam_system_s":"",
	 "desc_aerial_s":"",
	 "desc_generator_s":"",
	 "desc_compartment_door_s":"",
	 "pressure_governor_s":"",
	 "gross_vehicle_weight_rating_s":"",
	 "gross_weight_without_water_s":"",
	 "gross_weight_with_water_s":"",
	 "front_weight_rating_s":"",
	 "front_weight_without_water_s":"",
	 "front_weight_with_water_s":"",
	 "rear_weight_rating_s":"",
	 "rear_weight_without_water_s":"",
	 "rear_weight_with_water_s":"",
	 "seating_capacity_s":"",
	 "body_primary_paint_color_s":"",
	 "body_secondary_paint_color_s":"",
	 "aerial_paint_color_s":"",
	 "engine_serial_number_s":"",
	 "transmission_serial_number_s":"",
	 "transfer_case_serial_number_s":"",
	 "generator_serial_number_s":"",
	 "alternator_serial_number_s":"",
	 "pto_serial_number_s":"",
	 "pump_serial_number_s":"",
	 "tank_serial_number_s":"",
	 "aerial_serial_number_s":"",
	 "front_tire_serial_number_1_s":"",
	 "front_tire_serial_number_2_s":"",
	 "rear_tire_serial_number_1_s":"",
	 "rear_tire_serial_number_2_s":"",
	 "rear_tire_serial_number_3_s":"",
	 "rear_tire_serial_number_4_s":"",
	 "rear_tire_serial_number_5_s":"",
	 "rear_tire_serial_number_6_s":"",
	 "rear_tire_serial_number_7_s":"",
	 "rear_tire_serial_number_8_s":"",
	 "trail_tire_serial_number_1_s":"",
	 "trail_tire_serial_number_2_s":"",
	 "trail_tire_serial_number_3_s":"",
	 "trail_tire_serial_number_4_s":"",
	 "side_roll_protection_serial_number_s":"See Service Bulletin #189",
	 "multiplex_serial_number_s":"",
	 "front_axle_1_serial_number_s":"",
	 "front_axle_2_serial_number_s":"",
	 "rear_axle_1_serial_number_s":"",
	 "rear_axle_2_serial_number_s":"",
	 "salesman_name_s":"",
	 "dealer_number_s":"",
	 "contract_admin_name_s":"",
	 "dealer_name_s":"",
	 "customer_name_s":"CATHEDRAL CITY FIRE DEPARTMENT",
	 "customer_number_s":"357289",
	 "address_s":"32100 DESERT VISTA ROAD",
	 "city_s":"Cathedral City",
	 "state_s":"CA",
	 "zip_s":"92234",
	 "country_s":"US",
	 "chief_name_s":"",
	 "manual_id_s":"",
	 "manual_revision_id_s":"",
	 "assembly_revision_id_s":"",
	 "assembly_function_group_id_s":"",
	 "cit_timestamp":"2008-02-08T19:06:11.928Z",
	 "front_axle_2_serial_number_t":[
	  ""],
	 "rear_tire_serial_number_8":[
	  ""],
	 "job_number":[
	  "FC0009"],
	 "rear_tire_serial_number_7":[
	  ""],
	 "chassis_sales_option_description":[
	  ""],
	 "desc_pump":[
	  ""],
	 "job_number_t":[
	  "FC0009"],
	 "chief_name":[
	  ""],
	 "front_axle_2_serial_number":[
	  ""],
	 "contract_admin_name":[
	  ""],
	 "trail_tire_serial_number_3":[
	  ""],
	 "trail_tire_serial_number_4":[
	  ""],
	 "trail_tire_serial_number_1":[
	  ""],
	 "city":[
	  "Cathedral City"],
	 "trail_tire_serial_number_2":[
	  ""],
	 "rear_weight_with_water_t":[
	  ""],
	 "manual_id_t":[
	  ""],
	 "truck_vin_partial":[
	  ""],
	 "desc_engine_t":[
	  ""],
	 "generator_serial_number":[
	  ""],
	 "number_of_units_t":[
	  "1"],
	 "rear_weight_without_water_t":[
	  ""],
	 "rear_tire_serial_number_1":[
	  ""],
	 "rear_tire_serial_number_2":[
	  ""],
	 "rear_tire_serial_number_3":[
	  ""],
	 "rear_tire_serial_number_1_t":[
	  ""],
	 "aerial_paint_color_t":[
	  ""],
	 "dealer_name":[
	  ""],
	 "rear_tire_serial_number_4":[
	  ""],
	 "multiplex_serial_number_t":[
	  ""],
	 "rear_tire_serial_number_5":[
	  ""],
	 "rear_tire_serial_number_6":[
	  ""],
	 "desc_front_axle":[
	  ""],
	 "unit_number":[
	  "1"],
	 "truck_vin_partial_t":[
	  ""],
	 "tank_serial_number_t":[
	  ""],
	 "gross_vehicle_weight_rating":[
	  ""],
	 "desc_foam_system":[
	  ""],
	 "rear_tire_serial_number_3_t":[
	  ""],
	 "gross_weight_without_water_t":[
	  ""],
	 "salesman_name":[
	  ""],
	 "rear_tire_serial_number_5_t":[
	  ""],
	 "chassis_sales_option_description_t":[
	  ""],
	 "desc_engine":[
	  ""],
	 "pto_serial_number":[
	  ""],
	 "item_number_t":[
	  "FC0009"],
	 "gross_weight_with_water_t":[
	  ""],
	 "seating_capacity":[
	  ""],
	 "front_tire_serial_number_2_t":[
	  ""],
	 "pump_serial_number":[
	  ""],
	 "pressure_governor_t":[
	  ""],
	 "desc_front_tire":[
	  ""],
	 "account_id_t":[
	  "1 3 9946"],
	 "number_of_units":[
	  "1"],
	 "state_t":[
	  "CA"],
	 "desc_foam_system_t":[
	  ""],
	 "rear_axle_2_serial_number_t":[
	  ""],
	 "machine_id":[4],
	 "rear_weight_without_water":[
	  ""],
	 "desc_rear_axle_t":[
	  ""],
	 "body_primary_paint_color_t":[
	  ""],
	 "address_t":[
	  "32100 DESERT VISTA ROAD"],
	 "rear_axle_2_serial_number":[
	  ""],
	 "aerial_serial_number_t":[
	  ""],
	 "rear_tire_serial_number_2_t":[
	  ""],
	 "front_tire_serial_number_1":[
	  ""],
	 "drawing_number_t":[
	  ""],
	 "front_weight_rating_t":[
	  ""],
	 "front_tire_serial_number_2":[
	  ""],
	 "dealer_name_t":[
	  ""],
	 "salesman_name_t":[
	  ""],
	 "truck_vin_t":[
	  ""],
	 "gross_weight_with_water":[
	  ""],
	 "desc_transmission":[
	  ""],
	 "desc_tank":[
	  ""],
	 "desc_compartment_door":[
	  ""],
	 "transmission_serial_number_t":[
	  ""],
	 "dealer_number":[
	  ""],
	 "unit_number_t":[
	  "1"],
	 "chief_name_t":[
	  ""],
	 "aerial_serial_number":[
	  ""],
	 "front_weight_with_water_t":[
	  ""],
	 "generator_serial_number_t":[
	  ""],
	 "seating_capacity_t":[
	  ""],
	 "side_roll_protection_serial_number":[
	  "See Service Bulletin #189"],
	 "desc_tank_t":[
	  ""],
	 "actual_ship_date_t":[
	  "1999-10-31"],
	 "pump_serial_number_t":[
	  ""],
	 "desc_cab":[
	  ""],
	 "engine_serial_number":[
	  ""],
	 "trail_tire_serial_number_4_t":[
	  ""],
	 "manual_revision_id_t":[
	  ""],
	 "rear_weight_rating_t":[
	  ""],
	 "rear_axle_1_serial_number":[
	  ""],
	 "desc_rear_axle":[
	  ""],
	 "body_secondary_paint_color":[
	  ""],
	 "truck_vin":[
	  ""],
	 "front_axle_1_serial_number_t":[
	  ""],
	 "trail_tire_serial_number_2_t":[
	  ""],
	 "desc_cab_t":[
	  ""],
	 "dealer_number_t":[
	  ""],
	 "trail_tire_serial_number_1_t":[
	  ""],
	 "city_t":[
	  "Cathedral City"],
	 "front_weight_without_water_t":[
	  ""],
	 "desc_rear_tire_t":[
	  ""],
	 "account_id":[1,3,9946],
	 "side_roll_protection_serial_number_t":[
	  "See Service Bulletin #189"],
	 "actual_ship_date":[
	  "1999-10-31"],
	 "alternator_serial_number":[
	  ""],
	 "customer_name":[
	  "CATHEDRAL CITY FIRE DEPARTMENT"],
	 "transfer_case_serial_number":[
	  ""],
	 "desc_generator_t":[
	  ""],
	 "engine_serial_number_t":[
	  ""],
	 "alternator_serial_number_t":[
	  ""],
	 "country":[
	  "US"],
	 "rear_tire_serial_number_4_t":[
	  ""],
	 "desc_front_tire_t":[
	  ""],
	 "body_secondary_paint_color_t":[
	  ""],
	 "desc_rear_tire":[
	  ""],
	 "pto_serial_number_t":[
	  ""],
	 "trail_tire_serial_number_3_t":[
	  ""],
	 "desc_compartment_door_t":[
	  ""],
	 "desc_pump_t":[
	  ""],
	 "gross_weight_without_water":[
	  ""],
	 "assembly_function_group_id_t":[
	  ""],
	 "desc_aerial_t":[
	  ""],
	 "drawing_number":[
	  ""],
	 "item_number":[
	  "FC0009"],
	 "rear_weight_with_water":[
	  ""],
	 "rear_tire_serial_number_7_t":[
	  ""],
	 "work_order_t":[
	  ""],
	 "state":[
	  "CA"],
	 "work_order":[
	  ""],
	 "customer_name_t":[
	  "CATHEDRAL CITY FIRE DEPARTMENT"],
	 "rear_tire_serial_number_6_t":[
	  ""],
	 "tank_serial_number":[
	  ""],
	 "zip_t":[
	  "92234"],
	 "customer_number":[
	  "357289"],
	 "body_primary_paint_color":[
	  ""],
	 "rear_axle_1_serial_number_t":[
	  ""],
	 "body_sales_option_description":[
	  ""],
	 "aerial_paint_color":[
	  ""],
	 "desc_generator":[
	  ""],
	 "body_sales_option_description_t":[
	  ""],
	 "desc_aerial":[
	  ""],
	 "warranty_start_date":[
	  "1999-06-28"],
	 "transfer_case_serial_number_t":[
	  ""],
	 "zip":[
	  "92234"],
	 "transmission_serial_number":[
	  ""],
	 "machine_id_t":[
	  "4"],
	 "gross_vehicle_weight_rating_t":[
	  ""],
	 "country_t":[
	  "US"],
	 "desc_transmission_t":[
	  ""],
	 "pressure_governor":[
	  ""],
	 "front_weight_rating":[
	  ""],
	 "customer_number_t":[
	  "357289"],
	 "front_weight_without_water":[
	  ""],
	 "contract_admin_name_t":[
	  ""],
	 "front_axle_1_serial_number":[
	  ""],
	 "rear_tire_serial_number_8_t":[
	  ""],
	 "address":[
	  "32100 DESERT VISTA ROAD"],
	 "desc_front_axle_t":[
	  ""],
	 "assembly_revision_id_t":[
	  ""],
	 "front_tire_serial_number_1_t":[
	  ""],
	 "multiplex_serial_number":[
	  ""],
	 "front_weight_with_water":[
	  ""],
	 "warranty_start_date_t":[
	  "1999-06-28"],
	 "rear_weight_rating":[
	  ""]},
	{
	 "guid":"pierce/dev/my_fleet/36383",
	 "cit_client":"pierce",
	 "cit_instance":"dev",
	 "cit_domain":"my_fleet",
	 "account_id_s":"9946 17942 17948",
	 "machine_id_s":"36383",
	 "job_number_s":"19440",
	 "item_number_s":"",
	 "unit_number_s":"2",
	 "number_of_units_s":"3",
	 "work_order_s":"07831131",
	 "actual_ship_date_s":"2007-09-26",
	 "warranty_start_date_s":"2007-09-26",
	 "drawing_number_s":"19440AD",
	 "body_sales_option_description_s":"Aerial, 75' HD Ladder/HAL, Tandem/Quint, Alum Body",
	 "chassis_sales_option_description_s":"Dash-2000 Chassis, Aerials/Tankers Tandem 48K",
	 "truck_vin_s":"4P1CD01H57A007690",
	 "truck_vin_partial_s":"7A007690",
	 "desc_cab_s":"Cab, Dash-2000, 67\\" w/10\\" Raised Roof",
	 "desc_engine_s":"S60, 470 hp, 1650 torq, w/Jake, P/N 1466820, Dash/Lance, 2006 Allocated",
	 "desc_front_axle_s":"1674997                       Axle, Front, Oshkosh TAK-4, Non Drive, 19,500 lb,",
	 "desc_front_tire_s":"Tires, Michelin, 385/65R22.50 18 ply XTE2, Hiway Rib",
	 "desc_pump_s":"Pump, 2000 CSU Single Stage,Waterous",
	 "desc_rear_axle_s":"RD22145NFLF921                Axle, Rear, Meritor RT44-145, 44,000 lb",
	 "desc_rear_tire_s":"Tires, (8) Michelin, 11R22.50 16 ply, XZE",
	 "desc_tank_s":"Tank, Water, 500 Gallon, Poly, PAL",
	 "desc_transmission_s":"Trans, Allison Gen IV 4000 EVS PR",
	 "desc_foam_system_s":"No Foam System Required",
	 "desc_aerial_s":"Aerial, 75' Heavy Duty Ladder",
	 "desc_generator_s":"Onan 10 kW Hydraulic w/ Electronic Control, Hotshift PTO",
	 "desc_compartment_door_s":"Doors, Roll-up, Robinson - Side Compt",
	 "pressure_governor_s":"",
	 "gross_vehicle_weight_rating_s":"",
	 "gross_weight_without_water_s":"49240",
	 "gross_weight_with_water_s":"",
	 "front_weight_rating_s":"",
	 "front_weight_without_water_s":"16560",
	 "front_weight_with_water_s":"17160",
	 "rear_weight_rating_s":"",
	 "rear_weight_without_water_s":"32680",
	 "rear_weight_with_water_s":"36380",
	 "seating_capacity_s":"5",
	 "body_primary_paint_color_s":"#70 RED",
	 "body_secondary_paint_color_s":"",
	 "aerial_paint_color_s":"#10 WHITE",
	 "engine_serial_number_s":"06R0867551",
	 "transmission_serial_number_s":"6610232389",
	 "transfer_case_serial_number_s":"",
	 "generator_serial_number_s":"F070071321",
	 "alternator_serial_number_s":"10685",
	 "pto_serial_number_s":"",
	 "pump_serial_number_s":"129239",
	 "tank_serial_number_s":"D4UPFW0726070523",
	 "aerial_serial_number_s":"19440-02",
	 "front_tire_serial_number_1_s":"HAWBB5TX5006",
	 "front_tire_serial_number_2_s":"HAWBB5TX4006",
	 "rear_tire_serial_number_1_s":"B63TA8VX2407",
	 "rear_tire_serial_number_2_s":"B63TA8VX2507",
	 "rear_tire_serial_number_3_s":"",
	 "rear_tire_serial_number_4_s":"",
	 "rear_tire_serial_number_5_s":"",
	 "rear_tire_serial_number_6_s":"",
	 "rear_tire_serial_number_7_s":"",
	 "rear_tire_serial_number_8_s":"",
	 "trail_tire_serial_number_1_s":"",
	 "trail_tire_serial_number_2_s":"",
	 "trail_tire_serial_number_3_s":"",
	 "trail_tire_serial_number_4_s":"",
	 "side_roll_protection_serial_number_s":"See Service Bulletin #189",
	 "multiplex_serial_number_s":"",
	 "front_axle_1_serial_number_s":"N/A",
	 "front_axle_2_serial_number_s":"",
	 "rear_axle_1_serial_number_s":"NKA07022867",
	 "rear_axle_2_serial_number_s":"",
	 "salesman_name_s":"BROWN ROGER",
	 "dealer_number_s":"2216537",
	 "contract_admin_name_s":"MADER, GARY R",
	 "dealer_name_s":"CONRAD FIRE EQUIPMENT INC",
	 "customer_name_s":"WICHITA CITY OF",
	 "customer_number_s":"448596",
	 "address_s":"CITY OF WICHITA FIRE DEPARTMENT",
	 "city_s":"Wichita",
	 "state_s":"KS",
	 "zip_s":"67203",
	 "country_s":"US",
	 "chief_name_s":"KC LAWSON",
	 "manual_id_s":"",
	 "manual_revision_id_s":"",
	 "assembly_revision_id_s":"",
	 "assembly_function_group_id_s":"",
	 "cit_timestamp":"2008-02-08T19:06:11.943Z",
	 "front_axle_2_serial_number_t":[
	  ""],
	 "rear_tire_serial_number_8":[
	  ""],
	 "job_number":[
	  "19440"],
	 "rear_tire_serial_number_7":[
	  ""],
	 "chassis_sales_option_description":[
	  "Dash-2000 Chassis, Aerials/Tankers Tandem 48K"],
	 "desc_pump":[
	  "Pump, 2000 CSU Single Stage,Waterous"],
	 "job_number_t":[
	  "19440"],
	 "chief_name":[
	  "KC LAWSON"],
	 "front_axle_2_serial_number":[
	  ""],
	 "contract_admin_name":[
	  "MADER, GARY R"],
	 "trail_tire_serial_number_3":[
	  ""],
	 "trail_tire_serial_number_4":[
	  ""],
	 "trail_tire_serial_number_1":[
	  ""],
	 "city":[
	  "Wichita"],
	 "trail_tire_serial_number_2":[
	  ""],
	 "rear_weight_with_water_t":[
	  "36380"],
	 "manual_id_t":[
	  ""],
	 "truck_vin_partial":[
	  "7A007690"],
	 "desc_engine_t":[
	  "S60, 470 hp, 1650 torq, w/Jake, P/N 1466820, Dash/Lance, 2006 Allocated"],
	 "generator_serial_number":[
	  "F070071321"],
	 "number_of_units_t":[
	  "3"],
	 "rear_weight_without_water_t":[
	  "32680"],
	 "rear_tire_serial_number_1":[
	  "B63TA8VX2407"],
	 "rear_tire_serial_number_2":[
	  "B63TA8VX2507"],
	 "rear_tire_serial_number_3":[
	  ""],
	 "rear_tire_serial_number_1_t":[
	  "B63TA8VX2407"],
	 "aerial_paint_color_t":[
	  "#10 WHITE"],
	 "dealer_name":[
	  "CONRAD FIRE EQUIPMENT INC"],
	 "rear_tire_serial_number_4":[
	  ""],
	 "multiplex_serial_number_t":[
	  ""],
	 "rear_tire_serial_number_5":[
	  ""],
	 "rear_tire_serial_number_6":[
	  ""],
	 "desc_front_axle":[
	  "1674997                       Axle, Front, Oshkosh TAK-4, Non Drive, 19,500 lb,"],
	 "unit_number":[
	  "2"],
	 "truck_vin_partial_t":[
	  "7A007690"],
	 "tank_serial_number_t":[
	  "D4UPFW0726070523"],
	 "gross_vehicle_weight_rating":[
	  ""],
	 "desc_foam_system":[
	  "No Foam System Required"],
	 "rear_tire_serial_number_3_t":[
	  ""],
	 "gross_weight_without_water_t":[
	  "49240"],
	 "salesman_name":[
	  "BROWN ROGER"],
	 "rear_tire_serial_number_5_t":[
	  ""],
	 "chassis_sales_option_description_t":[
	  "Dash-2000 Chassis, Aerials/Tankers Tandem 48K"],
	 "desc_engine":[
	  "S60, 470 hp, 1650 torq, w/Jake, P/N 1466820, Dash/Lance, 2006 Allocated"],
	 "pto_serial_number":[
	  ""],
	 "item_number_t":[
	  ""],
	 "gross_weight_with_water_t":[
	  ""],
	 "seating_capacity":[
	  "5"],
	 "front_tire_serial_number_2_t":[
	  "HAWBB5TX4006"],
	 "pump_serial_number":[
	  "129239"],
	 "pressure_governor_t":[
	  ""],
	 "desc_front_tire":[
	  "Tires, Michelin, 385/65R22.50 18 ply XTE2, Hiway Rib"],
	 "account_id_t":[
	  "9946 17942 17948"],
	 "number_of_units":[
	  "3"],
	 "state_t":[
	  "KS"],
	 "desc_foam_system_t":[
	  "No Foam System Required"],
	 "rear_axle_2_serial_number_t":[
	  ""],
	 "machine_id":[36383],
	 "rear_weight_without_water":[
	  "32680"],
	 "desc_rear_axle_t":[
	  "RD22145NFLF921                Axle, Rear, Meritor RT44-145, 44,000 lb"],
	 "body_primary_paint_color_t":[
	  "#70 RED"],
	 "address_t":[
	  "CITY OF WICHITA FIRE DEPARTMENT"],
	 "rear_axle_2_serial_number":[
	  ""],
	 "aerial_serial_number_t":[
	  "19440-02"],
	 "rear_tire_serial_number_2_t":[
	  "B63TA8VX2507"],
	 "front_tire_serial_number_1":[
	  "HAWBB5TX5006"],
	 "drawing_number_t":[
	  "19440AD"],
	 "front_weight_rating_t":[
	  ""],
	 "front_tire_serial_number_2":[
	  "HAWBB5TX4006"],
	 "dealer_name_t":[
	  "CONRAD FIRE EQUIPMENT INC"],
	 "salesman_name_t":[
	  "BROWN ROGER"],
	 "truck_vin_t":[
	  "4P1CD01H57A007690"],
	 "gross_weight_with_water":[
	  ""],
	 "desc_transmission":[
	  "Trans, Allison Gen IV 4000 EVS PR"],
	 "desc_tank":[
	  "Tank, Water, 500 Gallon, Poly, PAL"],
	 "desc_compartment_door":[
	  "Doors, Roll-up, Robinson - Side Compt"],
	 "transmission_serial_number_t":[
	  "6610232389"],
	 "dealer_number":[
	  "2216537"],
	 "unit_number_t":[
	  "2"],
	 "chief_name_t":[
	  "KC LAWSON"],
	 "aerial_serial_number":[
	  "19440-02"],
	 "front_weight_with_water_t":[
	  "17160"],
	 "generator_serial_number_t":[
	  "F070071321"],
	 "seating_capacity_t":[
	  "5"],
	 "side_roll_protection_serial_number":[
	  "See Service Bulletin #189"],
	 "desc_tank_t":[
	  "Tank, Water, 500 Gallon, Poly, PAL"],
	 "actual_ship_date_t":[
	  "2007-09-26"],
	 "pump_serial_number_t":[
	  "129239"],
	 "desc_cab":[
	  "Cab, Dash-2000, 67\\" w/10\\" Raised Roof"],
	 "engine_serial_number":[
	  "06R0867551"],
	 "trail_tire_serial_number_4_t":[
	  ""],
	 "manual_revision_id_t":[
	  ""],
	 "rear_weight_rating_t":[
	  ""],
	 "rear_axle_1_serial_number":[
	  "NKA07022867"],
	 "desc_rear_axle":[
	  "RD22145NFLF921                Axle, Rear, Meritor RT44-145, 44,000 lb"],
	 "body_secondary_paint_color":[
	  ""],
	 "truck_vin":[
	  "4P1CD01H57A007690"],
	 "front_axle_1_serial_number_t":[
	  "N/A"],
	 "trail_tire_serial_number_2_t":[
	  ""],
	 "desc_cab_t":[
	  "Cab, Dash-2000, 67\\" w/10\\" Raised Roof"],
	 "dealer_number_t":[
	  "2216537"],
	 "trail_tire_serial_number_1_t":[
	  ""],
	 "city_t":[
	  "Wichita"],
	 "front_weight_without_water_t":[
	  "16560"],
	 "desc_rear_tire_t":[
	  "Tires, (8) Michelin, 11R22.50 16 ply, XZE"],
	 "account_id":[17942,9946,17948],
	 "side_roll_protection_serial_number_t":[
	  "See Service Bulletin #189"],
	 "actual_ship_date":[
	  "2007-09-26"],
	 "alternator_serial_number":[
	  "10685"],
	 "customer_name":[
	  "WICHITA CITY OF"],
	 "transfer_case_serial_number":[
	  ""],
	 "desc_generator_t":[
	  "Onan 10 kW Hydraulic w/ Electronic Control, Hotshift PTO"],
	 "engine_serial_number_t":[
	  "06R0867551"],
	 "alternator_serial_number_t":[
	  "10685"],
	 "country":[
	  "US"],
	 "rear_tire_serial_number_4_t":[
	  ""],
	 "desc_front_tire_t":[
	  "Tires, Michelin, 385/65R22.50 18 ply XTE2, Hiway Rib"],
	 "body_secondary_paint_color_t":[
	  ""],
	 "desc_rear_tire":[
	  "Tires, (8) Michelin, 11R22.50 16 ply, XZE"],
	 "pto_serial_number_t":[
	  ""],
	 "trail_tire_serial_number_3_t":[
	  ""],
	 "desc_compartment_door_t":[
	  "Doors, Roll-up, Robinson - Side Compt"],
	 "desc_pump_t":[
	  "Pump, 2000 CSU Single Stage,Waterous"],
	 "gross_weight_without_water":[
	  "49240"],
	 "assembly_function_group_id_t":[
	  ""],
	 "desc_aerial_t":[
	  "Aerial, 75' Heavy Duty Ladder"],
	 "drawing_number":[
	  "19440AD"],
	 "item_number":[
	  ""],
	 "rear_weight_with_water":[
	  "36380"],
	 "rear_tire_serial_number_7_t":[
	  ""],
	 "work_order_t":[
	  "07831131"],
	 "state":[
	  "KS"],
	 "work_order":[
	  "07831131"],
	 "customer_name_t":[
	  "WICHITA CITY OF"],
	 "rear_tire_serial_number_6_t":[
	  ""],
	 "tank_serial_number":[
	  "D4UPFW0726070523"],
	 "zip_t":[
	  "67203"],
	 "customer_number":[
	  "448596"],
	 "body_primary_paint_color":[
	  "#70 RED"],
	 "rear_axle_1_serial_number_t":[
	  "NKA07022867"],
	 "body_sales_option_description":[
	  "Aerial, 75' HD Ladder/HAL, Tandem/Quint, Alum Body"],
	 "aerial_paint_color":[
	  "#10 WHITE"],
	 "desc_generator":[
	  "Onan 10 kW Hydraulic w/ Electronic Control, Hotshift PTO"],
	 "body_sales_option_description_t":[
	  "Aerial, 75' HD Ladder/HAL, Tandem/Quint, Alum Body"],
	 "desc_aerial":[
	  "Aerial, 75' Heavy Duty Ladder"],
	 "warranty_start_date":[
	  "2007-09-26"],
	 "transfer_case_serial_number_t":[
	  ""],
	 "zip":[
	  "67203"],
	 "transmission_serial_number":[
	  "6610232389"],
	 "machine_id_t":[
	  "36383"],
	 "gross_vehicle_weight_rating_t":[
	  ""],
	 "country_t":[
	  "US"],
	 "desc_transmission_t":[
	  "Trans, Allison Gen IV 4000 EVS PR"],
	 "pressure_governor":[
	  ""],
	 "front_weight_rating":[
	  ""],
	 "customer_number_t":[
	  "448596"],
	 "front_weight_without_water":[
	  "16560"],
	 "contract_admin_name_t":[
	  "MADER, GARY R"],
	 "front_axle_1_serial_number":[
	  "N/A"],
	 "rear_tire_serial_number_8_t":[
	  ""],
	 "address":[
	  "CITY OF WICHITA FIRE DEPARTMENT"],
	 "desc_front_axle_t":[
	  "1674997                       Axle, Front, Oshkosh TAK-4, Non Drive, 19,500 lb,"],
	 "assembly_revision_id_t":[
	  ""],
	 "front_tire_serial_number_1_t":[
	  "HAWBB5TX5006"],
	 "multiplex_serial_number":[
	  ""],
	 "front_weight_with_water":[
	  "17160"],
	 "warranty_start_date_t":[
	  "2007-09-26"],
	 "rear_weight_rating":[
	  ""]},
	{
	 "guid":"pierce/dev/my_fleet/7",
	 "cit_client":"pierce",
	 "cit_instance":"dev",
	 "cit_domain":"my_fleet",
	 "account_id_s":"1 3 9946",
	 "machine_id_s":"7",
	 "job_number_s":"FC0509",
	 "item_number_s":"FC0509",
	 "unit_number_s":"1",
	 "number_of_units_s":"1",
	 "work_order_s":"",
	 "actual_ship_date_s":"0",
	 "warranty_start_date_s":"1999-06-28",
	 "drawing_number_s":"",
	 "body_sales_option_description_s":"",
	 "chassis_sales_option_description_s":"",
	 "truck_vin_s":"",
	 "truck_vin_partial_s":"",
	 "desc_cab_s":"",
	 "desc_engine_s":"",
	 "desc_front_axle_s":"",
	 "desc_front_tire_s":"",
	 "desc_pump_s":"",
	 "desc_rear_axle_s":"",
	 "desc_rear_tire_s":"",
	 "desc_tank_s":"",
	 "desc_transmission_s":"",
	 "desc_foam_system_s":"",
	 "desc_aerial_s":"",
	 "desc_generator_s":"",
	 "desc_compartment_door_s":"",
	 "pressure_governor_s":"",
	 "gross_vehicle_weight_rating_s":"",
	 "gross_weight_without_water_s":"",
	 "gross_weight_with_water_s":"",
	 "front_weight_rating_s":"",
	 "front_weight_without_water_s":"",
	 "front_weight_with_water_s":"",
	 "rear_weight_rating_s":"",
	 "rear_weight_without_water_s":"",
	 "rear_weight_with_water_s":"",
	 "seating_capacity_s":"",
	 "body_primary_paint_color_s":"",
	 "body_secondary_paint_color_s":"",
	 "aerial_paint_color_s":"",
	 "engine_serial_number_s":"",
	 "transmission_serial_number_s":"",
	 "transfer_case_serial_number_s":"",
	 "generator_serial_number_s":"",
	 "alternator_serial_number_s":"",
	 "pto_serial_number_s":"",
	 "pump_serial_number_s":"",
	 "tank_serial_number_s":"",
	 "aerial_serial_number_s":"",
	 "front_tire_serial_number_1_s":"",
	 "front_tire_serial_number_2_s":"",
	 "rear_tire_serial_number_1_s":"",
	 "rear_tire_serial_number_2_s":"",
	 "rear_tire_serial_number_3_s":"",
	 "rear_tire_serial_number_4_s":"",
	 "rear_tire_serial_number_5_s":"",
	 "rear_tire_serial_number_6_s":"",
	 "rear_tire_serial_number_7_s":"",
	 "rear_tire_serial_number_8_s":"",
	 "trail_tire_serial_number_1_s":"",
	 "trail_tire_serial_number_2_s":"",
	 "trail_tire_serial_number_3_s":"",
	 "trail_tire_serial_number_4_s":"",
	 "side_roll_protection_serial_number_s":"See Service Bulletin #189",
	 "multiplex_serial_number_s":"",
	 "front_axle_1_serial_number_s":"",
	 "front_axle_2_serial_number_s":"",
	 "rear_axle_1_serial_number_s":"",
	 "rear_axle_2_serial_number_s":"",
	 "salesman_name_s":"",
	 "dealer_number_s":"",
	 "contract_admin_name_s":"",
	 "dealer_name_s":"",
	 "customer_name_s":"BUREAU OF LAND MANAGEMENT",
	 "customer_number_s":"510504",
	 "address_s":"2800 COTTAGE WAY",
	 "city_s":"Sacramento",
	 "state_s":"CA",
	 "zip_s":"95825",
	 "country_s":"US",
	 "chief_name_s":"",
	 "manual_id_s":"",
	 "manual_revision_id_s":"",
	 "assembly_revision_id_s":"",
	 "assembly_function_group_id_s":"",
	 "cit_timestamp":"2008-02-08T19:06:11.966Z",
	 "front_axle_2_serial_number_t":[
	  ""],
	 "rear_tire_serial_number_8":[
	  ""],
	 "job_number":[
	  "FC0509"],
	 "rear_tire_serial_number_7":[
	  ""],
	 "chassis_sales_option_description":[
	  ""],
	 "desc_pump":[
	  ""],
	 "job_number_t":[
	  "FC0509"],
	 "chief_name":[
	  ""],
	 "front_axle_2_serial_number":[
	  ""],
	 "contract_admin_name":[
	  ""],
	 "trail_tire_serial_number_3":[
	  ""],
	 "trail_tire_serial_number_4":[
	  ""],
	 "trail_tire_serial_number_1":[
	  ""],
	 "city":[
	  "Sacramento"],
	 "trail_tire_serial_number_2":[
	  ""],
	 "rear_weight_with_water_t":[
	  ""],
	 "manual_id_t":[
	  ""],
	 "truck_vin_partial":[
	  ""],
	 "desc_engine_t":[
	  ""],
	 "generator_serial_number":[
	  ""],
	 "number_of_units_t":[
	  "1"],
	 "rear_weight_without_water_t":[
	  ""],
	 "rear_tire_serial_number_1":[
	  ""],
	 "rear_tire_serial_number_2":[
	  ""],
	 "rear_tire_serial_number_3":[
	  ""],
	 "rear_tire_serial_number_1_t":[
	  ""],
	 "aerial_paint_color_t":[
	  ""],
	 "dealer_name":[
	  ""],
	 "rear_tire_serial_number_4":[
	  ""],
	 "multiplex_serial_number_t":[
	  ""],
	 "rear_tire_serial_number_5":[
	  ""],
	 "rear_tire_serial_number_6":[
	  ""],
	 "desc_front_axle":[
	  ""],
	 "unit_number":[
	  "1"],
	 "truck_vin_partial_t":[
	  ""],
	 "tank_serial_number_t":[
	  ""],
	 "gross_vehicle_weight_rating":[
	  ""],
	 "desc_foam_system":[
	  ""],
	 "rear_tire_serial_number_3_t":[
	  ""],
	 "gross_weight_without_water_t":[
	  ""],
	 "salesman_name":[
	  ""],
	 "rear_tire_serial_number_5_t":[
	  ""],
	 "chassis_sales_option_description_t":[
	  ""],
	 "desc_engine":[
	  ""],
	 "pto_serial_number":[
	  ""],
	 "item_number_t":[
	  "FC0509"],
	 "gross_weight_with_water_t":[
	  ""],
	 "seating_capacity":[
	  ""],
	 "front_tire_serial_number_2_t":[
	  ""],
	 "pump_serial_number":[
	  ""],
	 "pressure_governor_t":[
	  ""],
	 "desc_front_tire":[
	  ""],
	 "account_id_t":[
	  "1 3 9946"],
	 "number_of_units":[
	  "1"],
	 "state_t":[
	  "CA"],
	 "desc_foam_system_t":[
	  ""],
	 "rear_axle_2_serial_number_t":[
	  ""],
	 "machine_id":[7],
	 "rear_weight_without_water":[
	  ""],
	 "desc_rear_axle_t":[
	  ""],
	 "body_primary_paint_color_t":[
	  ""],
	 "address_t":[
	  "2800 COTTAGE WAY"],
	 "rear_axle_2_serial_number":[
	  ""],
	 "aerial_serial_number_t":[
	  ""],
	 "rear_tire_serial_number_2_t":[
	  ""],
	 "front_tire_serial_number_1":[
	  ""],
	 "drawing_number_t":[
	  ""],
	 "front_weight_rating_t":[
	  ""],
	 "front_tire_serial_number_2":[
	  ""],
	 "dealer_name_t":[
	  ""],
	 "salesman_name_t":[
	  ""],
	 "truck_vin_t":[
	  ""],
	 "gross_weight_with_water":[
	  ""],
	 "desc_transmission":[
	  ""],
	 "desc_tank":[
	  ""],
	 "desc_compartment_door":[
	  ""],
	 "transmission_serial_number_t":[
	  ""],
	 "dealer_number":[
	  ""],
	 "unit_number_t":[
	  "1"],
	 "chief_name_t":[
	  ""],
	 "aerial_serial_number":[
	  ""],
	 "front_weight_with_water_t":[
	  ""],
	 "generator_serial_number_t":[
	  ""],
	 "seating_capacity_t":[
	  ""],
	 "side_roll_protection_serial_number":[
	  "See Service Bulletin #189"],
	 "desc_tank_t":[
	  ""],
	 "actual_ship_date_t":[
	  "0"],
	 "pump_serial_number_t":[
	  ""],
	 "desc_cab":[
	  ""],
	 "engine_serial_number":[
	  ""],
	 "trail_tire_serial_number_4_t":[
	  ""],
	 "manual_revision_id_t":[
	  ""],
	 "rear_weight_rating_t":[
	  ""],
	 "rear_axle_1_serial_number":[
	  ""],
	 "desc_rear_axle":[
	  ""],
	 "body_secondary_paint_color":[
	  ""],
	 "truck_vin":[
	  ""],
	 "front_axle_1_serial_number_t":[
	  ""],
	 "trail_tire_serial_number_2_t":[
	  ""],
	 "desc_cab_t":[
	  ""],
	 "dealer_number_t":[
	  ""],
	 "trail_tire_serial_number_1_t":[
	  ""],
	 "city_t":[
	  "Sacramento"],
	 "front_weight_without_water_t":[
	  ""],
	 "desc_rear_tire_t":[
	  ""],
	 "account_id":[1,3,9946],
	 "side_roll_protection_serial_number_t":[
	  "See Service Bulletin #189"],
	 "actual_ship_date":[
	  "0"],
	 "alternator_serial_number":[
	  ""],
	 "customer_name":[
	  "BUREAU OF LAND MANAGEMENT"],
	 "transfer_case_serial_number":[
	  ""],
	 "desc_generator_t":[
	  ""],
	 "engine_serial_number_t":[
	  ""],
	 "alternator_serial_number_t":[
	  ""],
	 "country":[
	  "US"],
	 "rear_tire_serial_number_4_t":[
	  ""],
	 "desc_front_tire_t":[
	  ""],
	 "body_secondary_paint_color_t":[
	  ""],
	 "desc_rear_tire":[
	  ""],
	 "pto_serial_number_t":[
	  ""],
	 "trail_tire_serial_number_3_t":[
	  ""],
	 "desc_compartment_door_t":[
	  ""],
	 "desc_pump_t":[
	  ""],
	 "gross_weight_without_water":[
	  ""],
	 "assembly_function_group_id_t":[
	  ""],
	 "desc_aerial_t":[
	  ""],
	 "drawing_number":[
	  ""],
	 "item_number":[
	  "FC0509"],
	 "rear_weight_with_water":[
	  ""],
	 "rear_tire_serial_number_7_t":[
	  ""],
	 "work_order_t":[
	  ""],
	 "state":[
	  "CA"],
	 "work_order":[
	  ""],
	 "customer_name_t":[
	  "BUREAU OF LAND MANAGEMENT"],
	 "rear_tire_serial_number_6_t":[
	  ""],
	 "tank_serial_number":[
	  ""],
	 "zip_t":[
	  "95825"],
	 "customer_number":[
	  "510504"],
	 "body_primary_paint_color":[
	  ""],
	 "rear_axle_1_serial_number_t":[
	  ""],
	 "body_sales_option_description":[
	  ""],
	 "aerial_paint_color":[
	  ""],
	 "desc_generator":[
	  ""],
	 "body_sales_option_description_t":[
	  ""],
	 "desc_aerial":[
	  ""],
	 "warranty_start_date":[
	  "1999-06-28"],
	 "transfer_case_serial_number_t":[
	  ""],
	 "zip":[
	  "95825"],
	 "transmission_serial_number":[
	  ""],
	 "machine_id_t":[
	  "7"],
	 "gross_vehicle_weight_rating_t":[
	  ""],
	 "country_t":[
	  "US"],
	 "desc_transmission_t":[
	  ""],
	 "pressure_governor":[
	  ""],
	 "front_weight_rating":[
	  ""],
	 "customer_number_t":[
	  "510504"],
	 "front_weight_without_water":[
	  ""],
	 "contract_admin_name_t":[
	  ""],
	 "front_axle_1_serial_number":[
	  ""],
	 "rear_tire_serial_number_8_t":[
	  ""],
	 "address":[
	  "2800 COTTAGE WAY"],
	 "desc_front_axle_t":[
	  ""],
	 "assembly_revision_id_t":[
	  ""],
	 "front_tire_serial_number_1_t":[
	  ""],
	 "multiplex_serial_number":[
	  ""],
	 "front_weight_with_water":[
	  ""],
	 "warranty_start_date_t":[
	  "1999-06-28"],
	 "rear_weight_rating":[
	  ""]},
	{
	 "guid":"pierce/dev/my_fleet/36382",
	 "cit_client":"pierce",
	 "cit_instance":"dev",
	 "cit_domain":"my_fleet",
	 "account_id_s":"9946 17942 17948",
	 "machine_id_s":"36382",
	 "job_number_s":"19440",
	 "item_number_s":"",
	 "unit_number_s":"1",
	 "number_of_units_s":"3",
	 "work_order_s":"07831122",
	 "actual_ship_date_s":"2007-09-25",
	 "warranty_start_date_s":"2007-09-25",
	 "drawing_number_s":"19440AD",
	 "body_sales_option_description_s":"Aerial, 75' HD Ladder/HAL, Tandem/Quint, Alum Body",
	 "chassis_sales_option_description_s":"Dash-2000 Chassis, Aerials/Tankers Tandem 48K",
	 "truck_vin_s":"4P1CD01H97A007689",
	 "truck_vin_partial_s":"7A007689",
	 "desc_cab_s":"Cab, Dash-2000, 67\\" w/10\\" Raised Roof",
	 "desc_engine_s":"S60, 470 hp, 1650 torq, w/Jake, P/N 1466820, Dash/Lance, 2006 Allocated",
	 "desc_front_axle_s":"1674997                       Axle, Front, Oshkosh TAK-4, Non Drive, 19,500 lb,",
	 "desc_front_tire_s":"Tires, Michelin, 385/65R22.50 18 ply XTE2, Hiway Rib",
	 "desc_pump_s":"Pump, 2000 CSU Single Stage,Waterous",
	 "desc_rear_axle_s":"RD22145NFLF921                Axle, Rear, Meritor RT44-145, 44,000 lb",
	 "desc_rear_tire_s":"Tires, (8) Michelin, 11R22.50 16 ply, XZE",
	 "desc_tank_s":"Tank, Water, 500 Gallon, Poly, PAL",
	 "desc_transmission_s":"Trans, Allison Gen IV 4000 EVS PR",
	 "desc_foam_system_s":"No Foam System Required",
	 "desc_aerial_s":"Aerial, 75' Heavy Duty Ladder",
	 "desc_generator_s":"Onan 10 kW Hydraulic w/ Electronic Control, Hotshift PTO",
	 "desc_compartment_door_s":"Doors, Roll-up, Robinson - Side Compt",
	 "pressure_governor_s":"",
	 "gross_vehicle_weight_rating_s":"",
	 "gross_weight_without_water_s":"49240",
	 "gross_weight_with_water_s":"",
	 "front_weight_rating_s":"",
	 "front_weight_without_water_s":"16560",
	 "front_weight_with_water_s":"17160",
	 "rear_weight_rating_s":"",
	 "rear_weight_without_water_s":"32680",
	 "rear_weight_with_water_s":"36380",
	 "seating_capacity_s":"5",
	 "body_primary_paint_color_s":"#70 RED",
	 "body_secondary_paint_color_s":"",
	 "aerial_paint_color_s":"#10 WHITE",
	 "engine_serial_number_s":"06R0924244",
	 "transmission_serial_number_s":"6610231996",
	 "transfer_case_serial_number_s":"N/A",
	 "generator_serial_number_s":"D0070052999",
	 "alternator_serial_number_s":"10683",
	 "pto_serial_number_s":"",
	 "pump_serial_number_s":"129125",
	 "tank_serial_number_s":"D4UPFW0726070522",
	 "aerial_serial_number_s":"19440-01",
	 "front_tire_serial_number_1_s":"HAWBB5TX4006",
	 "front_tire_serial_number_2_s":"",
	 "rear_tire_serial_number_1_s":"B63TA8VX2407",
	 "rear_tire_serial_number_2_s":"B63TA8VX2507",
	 "rear_tire_serial_number_3_s":"",
	 "rear_tire_serial_number_4_s":"",
	 "rear_tire_serial_number_5_s":"",
	 "rear_tire_serial_number_6_s":"",
	 "rear_tire_serial_number_7_s":"",
	 "rear_tire_serial_number_8_s":"",
	 "trail_tire_serial_number_1_s":"",
	 "trail_tire_serial_number_2_s":"",
	 "trail_tire_serial_number_3_s":"",
	 "trail_tire_serial_number_4_s":"",
	 "side_roll_protection_serial_number_s":"See Service Bulletin #189",
	 "multiplex_serial_number_s":"",
	 "front_axle_1_serial_number_s":"N/A",
	 "front_axle_2_serial_number_s":"",
	 "rear_axle_1_serial_number_s":"NKA07022866",
	 "rear_axle_2_serial_number_s":"",
	 "salesman_name_s":"BROWN ROGER",
	 "dealer_number_s":"2216537",
	 "contract_admin_name_s":"MADER, GARY R",
	 "dealer_name_s":"CONRAD FIRE EQUIPMENT INC",
	 "customer_name_s":"WICHITA CITY OF",
	 "customer_number_s":"448596",
	 "address_s":"CITY OF WICHITA FIRE DEPARTMENT",
	 "city_s":"Wichita",
	 "state_s":"KS",
	 "zip_s":"67203",
	 "country_s":"US",
	 "chief_name_s":"KC LAWSON",
	 "manual_id_s":"",
	 "manual_revision_id_s":"",
	 "assembly_revision_id_s":"",
	 "assembly_function_group_id_s":"",
	 "cit_timestamp":"2008-02-08T19:06:12.008Z",
	 "front_axle_2_serial_number_t":[
	  ""],
	 "rear_tire_serial_number_8":[
	  ""],
	 "job_number":[
	  "19440"],
	 "rear_tire_serial_number_7":[
	  ""],
	 "chassis_sales_option_description":[
	  "Dash-2000 Chassis, Aerials/Tankers Tandem 48K"],
	 "desc_pump":[
	  "Pump, 2000 CSU Single Stage,Waterous"],
	 "job_number_t":[
	  "19440"],
	 "chief_name":[
	  "KC LAWSON"],
	 "front_axle_2_serial_number":[
	  ""],
	 "contract_admin_name":[
	  "MADER, GARY R"],
	 "trail_tire_serial_number_3":[
	  ""],
	 "trail_tire_serial_number_4":[
	  ""],
	 "trail_tire_serial_number_1":[
	  ""],
	 "city":[
	  "Wichita"],
	 "trail_tire_serial_number_2":[
	  ""],
	 "rear_weight_with_water_t":[
	  "36380"],
	 "manual_id_t":[
	  ""],
	 "truck_vin_partial":[
	  "7A007689"],
	 "desc_engine_t":[
	  "S60, 470 hp, 1650 torq, w/Jake, P/N 1466820, Dash/Lance, 2006 Allocated"],
	 "generator_serial_number":[
	  "D0070052999"],
	 "number_of_units_t":[
	  "3"],
	 "rear_weight_without_water_t":[
	  "32680"],
	 "rear_tire_serial_number_1":[
	  "B63TA8VX2407"],
	 "rear_tire_serial_number_2":[
	  "B63TA8VX2507"],
	 "rear_tire_serial_number_3":[
	  ""],
	 "rear_tire_serial_number_1_t":[
	  "B63TA8VX2407"],
	 "aerial_paint_color_t":[
	  "#10 WHITE"],
	 "dealer_name":[
	  "CONRAD FIRE EQUIPMENT INC"],
	 "rear_tire_serial_number_4":[
	  ""],
	 "multiplex_serial_number_t":[
	  ""],
	 "rear_tire_serial_number_5":[
	  ""],
	 "rear_tire_serial_number_6":[
	  ""],
	 "desc_front_axle":[
	  "1674997                       Axle, Front, Oshkosh TAK-4, Non Drive, 19,500 lb,"],
	 "unit_number":[
	  "1"],
	 "truck_vin_partial_t":[
	  "7A007689"],
	 "tank_serial_number_t":[
	  "D4UPFW0726070522"],
	 "gross_vehicle_weight_rating":[
	  ""],
	 "desc_foam_system":[
	  "No Foam System Required"],
	 "rear_tire_serial_number_3_t":[
	  ""],
	 "gross_weight_without_water_t":[
	  "49240"],
	 "salesman_name":[
	  "BROWN ROGER"],
	 "rear_tire_serial_number_5_t":[
	  ""],
	 "chassis_sales_option_description_t":[
	  "Dash-2000 Chassis, Aerials/Tankers Tandem 48K"],
	 "desc_engine":[
	  "S60, 470 hp, 1650 torq, w/Jake, P/N 1466820, Dash/Lance, 2006 Allocated"],
	 "pto_serial_number":[
	  ""],
	 "item_number_t":[
	  ""],
	 "gross_weight_with_water_t":[
	  ""],
	 "seating_capacity":[
	  "5"],
	 "front_tire_serial_number_2_t":[
	  ""],
	 "pump_serial_number":[
	  "129125"],
	 "pressure_governor_t":[
	  ""],
	 "desc_front_tire":[
	  "Tires, Michelin, 385/65R22.50 18 ply XTE2, Hiway Rib"],
	 "account_id_t":[
	  "9946 17942 17948"],
	 "number_of_units":[
	  "3"],
	 "state_t":[
	  "KS"],
	 "desc_foam_system_t":[
	  "No Foam System Required"],
	 "rear_axle_2_serial_number_t":[
	  ""],
	 "machine_id":[36382],
	 "rear_weight_without_water":[
	  "32680"],
	 "desc_rear_axle_t":[
	  "RD22145NFLF921                Axle, Rear, Meritor RT44-145, 44,000 lb"],
	 "body_primary_paint_color_t":[
	  "#70 RED"],
	 "address_t":[
	  "CITY OF WICHITA FIRE DEPARTMENT"],
	 "rear_axle_2_serial_number":[
	  ""],
	 "aerial_serial_number_t":[
	  "19440-01"],
	 "rear_tire_serial_number_2_t":[
	  "B63TA8VX2507"],
	 "front_tire_serial_number_1":[
	  "HAWBB5TX4006"],
	 "drawing_number_t":[
	  "19440AD"],
	 "front_weight_rating_t":[
	  ""],
	 "front_tire_serial_number_2":[
	  ""],
	 "dealer_name_t":[
	  "CONRAD FIRE EQUIPMENT INC"],
	 "salesman_name_t":[
	  "BROWN ROGER"],
	 "truck_vin_t":[
	  "4P1CD01H97A007689"],
	 "gross_weight_with_water":[
	  ""],
	 "desc_transmission":[
	  "Trans, Allison Gen IV 4000 EVS PR"],
	 "desc_tank":[
	  "Tank, Water, 500 Gallon, Poly, PAL"],
	 "desc_compartment_door":[
	  "Doors, Roll-up, Robinson - Side Compt"],
	 "transmission_serial_number_t":[
	  "6610231996"],
	 "dealer_number":[
	  "2216537"],
	 "unit_number_t":[
	  "1"],
	 "chief_name_t":[
	  "KC LAWSON"],
	 "aerial_serial_number":[
	  "19440-01"],
	 "front_weight_with_water_t":[
	  "17160"],
	 "generator_serial_number_t":[
	  "D0070052999"],
	 "seating_capacity_t":[
	  "5"],
	 "side_roll_protection_serial_number":[
	  "See Service Bulletin #189"],
	 "desc_tank_t":[
	  "Tank, Water, 500 Gallon, Poly, PAL"],
	 "actual_ship_date_t":[
	  "2007-09-25"],
	 "pump_serial_number_t":[
	  "129125"],
	 "desc_cab":[
	  "Cab, Dash-2000, 67\\" w/10\\" Raised Roof"],
	 "engine_serial_number":[
	  "06R0924244"],
	 "trail_tire_serial_number_4_t":[
	  ""],
	 "manual_revision_id_t":[
	  ""],
	 "rear_weight_rating_t":[
	  ""],
	 "rear_axle_1_serial_number":[
	  "NKA07022866"],
	 "desc_rear_axle":[
	  "RD22145NFLF921                Axle, Rear, Meritor RT44-145, 44,000 lb"],
	 "body_secondary_paint_color":[
	  ""],
	 "truck_vin":[
	  "4P1CD01H97A007689"],
	 "front_axle_1_serial_number_t":[
	  "N/A"],
	 "trail_tire_serial_number_2_t":[
	  ""],
	 "desc_cab_t":[
	  "Cab, Dash-2000, 67\\" w/10\\" Raised Roof"],
	 "dealer_number_t":[
	  "2216537"],
	 "trail_tire_serial_number_1_t":[
	  ""],
	 "city_t":[
	  "Wichita"],
	 "front_weight_without_water_t":[
	  "16560"],
	 "desc_rear_tire_t":[
	  "Tires, (8) Michelin, 11R22.50 16 ply, XZE"],
	 "account_id":[17942,9946,17948],
	 "side_roll_protection_serial_number_t":[
	  "See Service Bulletin #189"],
	 "actual_ship_date":[
	  "2007-09-25"],
	 "alternator_serial_number":[
	  "10683"],
	 "customer_name":[
	  "WICHITA CITY OF"],
	 "transfer_case_serial_number":[
	  "N/A"],
	 "desc_generator_t":[
	  "Onan 10 kW Hydraulic w/ Electronic Control, Hotshift PTO"],
	 "engine_serial_number_t":[
	  "06R0924244"],
	 "alternator_serial_number_t":[
	  "10683"],
	 "country":[
	  "US"],
	 "rear_tire_serial_number_4_t":[
	  ""],
	 "desc_front_tire_t":[
	  "Tires, Michelin, 385/65R22.50 18 ply XTE2, Hiway Rib"],
	 "body_secondary_paint_color_t":[
	  ""],
	 "desc_rear_tire":[
	  "Tires, (8) Michelin, 11R22.50 16 ply, XZE"],
	 "pto_serial_number_t":[
	  ""],
	 "trail_tire_serial_number_3_t":[
	  ""],
	 "desc_compartment_door_t":[
	  "Doors, Roll-up, Robinson - Side Compt"],
	 "desc_pump_t":[
	  "Pump, 2000 CSU Single Stage,Waterous"],
	 "gross_weight_without_water":[
	  "49240"],
	 "assembly_function_group_id_t":[
	  ""],
	 "desc_aerial_t":[
	  "Aerial, 75' Heavy Duty Ladder"],
	 "drawing_number":[
	  "19440AD"],
	 "item_number":[
	  ""],
	 "rear_weight_with_water":[
	  "36380"],
	 "rear_tire_serial_number_7_t":[
	  ""],
	 "work_order_t":[
	  "07831122"],
	 "state":[
	  "KS"],
	 "work_order":[
	  "07831122"],
	 "customer_name_t":[
	  "WICHITA CITY OF"],
	 "rear_tire_serial_number_6_t":[
	  ""],
	 "tank_serial_number":[
	  "D4UPFW0726070522"],
	 "zip_t":[
	  "67203"],
	 "customer_number":[
	  "448596"],
	 "body_primary_paint_color":[
	  "#70 RED"],
	 "rear_axle_1_serial_number_t":[
	  "NKA07022866"],
	 "body_sales_option_description":[
	  "Aerial, 75' HD Ladder/HAL, Tandem/Quint, Alum Body"],
	 "aerial_paint_color":[
	  "#10 WHITE"],
	 "desc_generator":[
	  "Onan 10 kW Hydraulic w/ Electronic Control, Hotshift PTO"],
	 "body_sales_option_description_t":[
	  "Aerial, 75' HD Ladder/HAL, Tandem/Quint, Alum Body"],
	 "desc_aerial":[
	  "Aerial, 75' Heavy Duty Ladder"],
	 "warranty_start_date":[
	  "2007-09-25"],
	 "transfer_case_serial_number_t":[
	  "N/A"],
	 "zip":[
	  "67203"],
	 "transmission_serial_number":[
	  "6610231996"],
	 "machine_id_t":[
	  "36382"],
	 "gross_vehicle_weight_rating_t":[
	  ""],
	 "country_t":[
	  "US"],
	 "desc_transmission_t":[
	  "Trans, Allison Gen IV 4000 EVS PR"],
	 "pressure_governor":[
	  ""],
	 "front_weight_rating":[
	  ""],
	 "customer_number_t":[
	  "448596"],
	 "front_weight_without_water":[
	  "16560"],
	 "contract_admin_name_t":[
	  "MADER, GARY R"],
	 "front_axle_1_serial_number":[
	  "N/A"],
	 "rear_tire_serial_number_8_t":[
	  ""],
	 "address":[
	  "CITY OF WICHITA FIRE DEPARTMENT"],
	 "desc_front_axle_t":[
	  "1674997                       Axle, Front, Oshkosh TAK-4, Non Drive, 19,500 lb,"],
	 "assembly_revision_id_t":[
	  ""],
	 "front_tire_serial_number_1_t":[
	  "HAWBB5TX4006"],
	 "multiplex_serial_number":[
	  ""],
	 "front_weight_with_water":[
	  "17160"],
	 "warranty_start_date_t":[
	  "2007-09-25"],
	 "rear_weight_rating":[
	  ""]},
	{
	 "guid":"pierce/dev/my_fleet/36381",
	 "cit_client":"pierce",
	 "cit_instance":"dev",
	 "cit_domain":"my_fleet",
	 "account_id_s":"9946 17942 17948",
	 "machine_id_s":"36381",
	 "job_number_s":"19437",
	 "item_number_s":"",
	 "unit_number_s":"2",
	 "number_of_units_s":"2",
	 "work_order_s":"07831103",
	 "actual_ship_date_s":"2007-09-24",
	 "warranty_start_date_s":"2007-09-24",
	 "drawing_number_s":"19437AD",
	 "body_sales_option_description_s":"Pumper, Long, Alum, 2nd Gen",
	 "chassis_sales_option_description_s":"Dash-2000 Chassis",
	 "truck_vin_s":"4P1CD01H57A007740",
	 "truck_vin_partial_s":"7A007740",
	 "desc_cab_s":"Cab, Dash-2000, 67\\" w/16\\" Raised Roof",
	 "desc_engine_s":"S60, 470 hp, 1650 torq, w/Jake, P/N 1394129, Quantum, 2006 Allocated",
	 "desc_front_axle_s":"1674997                       Axle, Front, Oshkosh TAK-4, Non Drive, 19,500 lb,",
	 "desc_front_tire_s":"Tires, Michelin, 385/65R22.50 18 ply XTE2, Hiway Rib",
	 "desc_pump_s":"Pump, 2000 CSU Single Stage,Waterous",
	 "desc_rear_axle_s":"RS25160KFPF111                Axle, Rear, Meritor RS25-160, 27,000 lb",
	 "desc_rear_tire_s":"Tires, (4) Michelin, 12R22.50 16 ply, XZY 3",
	 "desc_tank_s":"Tank, Water, 600 Gallon, Poly, Long",
	 "desc_transmission_s":"Trans, Allison Gen IV 4000 EVS PR",
	 "desc_foam_system_s":"No Foam System Required",
	 "desc_aerial_s":"",
	 "desc_generator_s":"Onan 8.0 kW Hydraulic w/ Electronic Control, Hotshift PTO",
	 "desc_compartment_door_s":"Doors, Roll-up, Robinson, Body",
	 "pressure_governor_s":"",
	 "gross_vehicle_weight_rating_s":"",
	 "gross_weight_without_water_s":"30560",
	 "gross_weight_with_water_s":"",
	 "front_weight_rating_s":"",
	 "front_weight_without_water_s":"17220",
	 "front_weight_with_water_s":"17600",
	 "rear_weight_rating_s":"",
	 "rear_weight_without_water_s":"13340",
	 "rear_weight_with_water_s":"18000",
	 "seating_capacity_s":"5",
	 "body_primary_paint_color_s":"#70 RED",
	 "body_secondary_paint_color_s":"",
	 "aerial_paint_color_s":"",
	 "engine_serial_number_s":"06R0924204",
	 "transmission_serial_number_s":"6610233417",
	 "transfer_case_serial_number_s":"N/A",
	 "generator_serial_number_s":"F070073171",
	 "alternator_serial_number_s":"10706",
	 "pto_serial_number_s":"",
	 "pump_serial_number_s":"129302",
	 "tank_serial_number_s":"D4UPFW0809070630",
	 "aerial_serial_number_s":"",
	 "front_tire_serial_number_1_s":"HAWBB5TX5006",
	 "front_tire_serial_number_2_s":"HAWBB5TX4406",
	 "rear_tire_serial_number_1_s":"B63XPAKX2407",
	 "rear_tire_serial_number_2_s":"B63XPAKX2107",
	 "rear_tire_serial_number_3_s":"B63XPAKX2307",
	 "rear_tire_serial_number_4_s":"",
	 "rear_tire_serial_number_5_s":"",
	 "rear_tire_serial_number_6_s":"",
	 "rear_tire_serial_number_7_s":"",
	 "rear_tire_serial_number_8_s":"",
	 "trail_tire_serial_number_1_s":"",
	 "trail_tire_serial_number_2_s":"",
	 "trail_tire_serial_number_3_s":"",
	 "trail_tire_serial_number_4_s":"",
	 "side_roll_protection_serial_number_s":"See Service Bulletin #189",
	 "multiplex_serial_number_s":"",
	 "front_axle_1_serial_number_s":"N/A",
	 "front_axle_2_serial_number_s":"",
	 "rear_axle_1_serial_number_s":"NKA07024407",
	 "rear_axle_2_serial_number_s":"",
	 "salesman_name_s":"BROWN ROGER",
	 "dealer_number_s":"2216537",
	 "contract_admin_name_s":"WHITE, BRADLEY J",
	 "dealer_name_s":"CONRAD FIRE EQUIPMENT INC",
	 "customer_name_s":"WICHITA CITY OF",
	 "customer_number_s":"448596",
	 "address_s":"CITY OF WICHITA FIRE DEPARTMENT",
	 "city_s":"Wichita",
	 "state_s":"KS",
	 "zip_s":"67203",
	 "country_s":"US",
	 "chief_name_s":"KC LAWSON",
	 "manual_id_s":"",
	 "manual_revision_id_s":"",
	 "assembly_revision_id_s":"",
	 "assembly_function_group_id_s":"",
	 "cit_timestamp":"2008-02-08T19:06:12.030Z",
	 "front_axle_2_serial_number_t":[
	  ""],
	 "rear_tire_serial_number_8":[
	  ""],
	 "job_number":[
	  "19437"],
	 "rear_tire_serial_number_7":[
	  ""],
	 "chassis_sales_option_description":[
	  "Dash-2000 Chassis"],
	 "desc_pump":[
	  "Pump, 2000 CSU Single Stage,Waterous"],
	 "job_number_t":[
	  "19437"],
	 "chief_name":[
	  "KC LAWSON"],
	 "front_axle_2_serial_number":[
	  ""],
	 "contract_admin_name":[
	  "WHITE, BRADLEY J"],
	 "trail_tire_serial_number_3":[
	  ""],
	 "trail_tire_serial_number_4":[
	  ""],
	 "trail_tire_serial_number_1":[
	  ""],
	 "city":[
	  "Wichita"],
	 "trail_tire_serial_number_2":[
	  ""],
	 "rear_weight_with_water_t":[
	  "18000"],
	 "manual_id_t":[
	  ""],
	 "truck_vin_partial":[
	  "7A007740"],
	 "desc_engine_t":[
	  "S60, 470 hp, 1650 torq, w/Jake, P/N 1394129, Quantum, 2006 Allocated"],
	 "generator_serial_number":[
	  "F070073171"],
	 "number_of_units_t":[
	  "2"],
	 "rear_weight_without_water_t":[
	  "13340"],
	 "rear_tire_serial_number_1":[
	  "B63XPAKX2407"],
	 "rear_tire_serial_number_2":[
	  "B63XPAKX2107"],
	 "rear_tire_serial_number_3":[
	  "B63XPAKX2307"],
	 "rear_tire_serial_number_1_t":[
	  "B63XPAKX2407"],
	 "aerial_paint_color_t":[
	  ""],
	 "dealer_name":[
	  "CONRAD FIRE EQUIPMENT INC"],
	 "rear_tire_serial_number_4":[
	  ""],
	 "multiplex_serial_number_t":[
	  ""],
	 "rear_tire_serial_number_5":[
	  ""],
	 "rear_tire_serial_number_6":[
	  ""],
	 "desc_front_axle":[
	  "1674997                       Axle, Front, Oshkosh TAK-4, Non Drive, 19,500 lb,"],
	 "unit_number":[
	  "2"],
	 "truck_vin_partial_t":[
	  "7A007740"],
	 "tank_serial_number_t":[
	  "D4UPFW0809070630"],
	 "gross_vehicle_weight_rating":[
	  ""],
	 "desc_foam_system":[
	  "No Foam System Required"],
	 "rear_tire_serial_number_3_t":[
	  "B63XPAKX2307"],
	 "gross_weight_without_water_t":[
	  "30560"],
	 "salesman_name":[
	  "BROWN ROGER"],
	 "rear_tire_serial_number_5_t":[
	  ""],
	 "chassis_sales_option_description_t":[
	  "Dash-2000 Chassis"],
	 "desc_engine":[
	  "S60, 470 hp, 1650 torq, w/Jake, P/N 1394129, Quantum, 2006 Allocated"],
	 "pto_serial_number":[
	  ""],
	 "item_number_t":[
	  ""],
	 "gross_weight_with_water_t":[
	  ""],
	 "seating_capacity":[
	  "5"],
	 "front_tire_serial_number_2_t":[
	  "HAWBB5TX4406"],
	 "pump_serial_number":[
	  "129302"],
	 "pressure_governor_t":[
	  ""],
	 "desc_front_tire":[
	  "Tires, Michelin, 385/65R22.50 18 ply XTE2, Hiway Rib"],
	 "account_id_t":[
	  "9946 17942 17948"],
	 "number_of_units":[
	  "2"],
	 "state_t":[
	  "KS"],
	 "desc_foam_system_t":[
	  "No Foam System Required"],
	 "rear_axle_2_serial_number_t":[
	  ""],
	 "machine_id":[36381],
	 "rear_weight_without_water":[
	  "13340"],
	 "desc_rear_axle_t":[
	  "RS25160KFPF111                Axle, Rear, Meritor RS25-160, 27,000 lb"],
	 "body_primary_paint_color_t":[
	  "#70 RED"],
	 "address_t":[
	  "CITY OF WICHITA FIRE DEPARTMENT"],
	 "rear_axle_2_serial_number":[
	  ""],
	 "aerial_serial_number_t":[
	  ""],
	 "rear_tire_serial_number_2_t":[
	  "B63XPAKX2107"],
	 "front_tire_serial_number_1":[
	  "HAWBB5TX5006"],
	 "drawing_number_t":[
	  "19437AD"],
	 "front_weight_rating_t":[
	  ""],
	 "front_tire_serial_number_2":[
	  "HAWBB5TX4406"],
	 "dealer_name_t":[
	  "CONRAD FIRE EQUIPMENT INC"],
	 "salesman_name_t":[
	  "BROWN ROGER"],
	 "truck_vin_t":[
	  "4P1CD01H57A007740"],
	 "gross_weight_with_water":[
	  ""],
	 "desc_transmission":[
	  "Trans, Allison Gen IV 4000 EVS PR"],
	 "desc_tank":[
	  "Tank, Water, 600 Gallon, Poly, Long"],
	 "desc_compartment_door":[
	  "Doors, Roll-up, Robinson, Body"],
	 "transmission_serial_number_t":[
	  "6610233417"],
	 "dealer_number":[
	  "2216537"],
	 "unit_number_t":[
	  "2"],
	 "chief_name_t":[
	  "KC LAWSON"],
	 "aerial_serial_number":[
	  ""],
	 "front_weight_with_water_t":[
	  "17600"],
	 "generator_serial_number_t":[
	  "F070073171"],
	 "seating_capacity_t":[
	  "5"],
	 "side_roll_protection_serial_number":[
	  "See Service Bulletin #189"],
	 "desc_tank_t":[
	  "Tank, Water, 600 Gallon, Poly, Long"],
	 "actual_ship_date_t":[
	  "2007-09-24"],
	 "pump_serial_number_t":[
	  "129302"],
	 "desc_cab":[
	  "Cab, Dash-2000, 67\\" w/16\\" Raised Roof"],
	 "engine_serial_number":[
	  "06R0924204"],
	 "trail_tire_serial_number_4_t":[
	  ""],
	 "manual_revision_id_t":[
	  ""],
	 "rear_weight_rating_t":[
	  ""],
	 "rear_axle_1_serial_number":[
	  "NKA07024407"],
	 "desc_rear_axle":[
	  "RS25160KFPF111                Axle, Rear, Meritor RS25-160, 27,000 lb"],
	 "body_secondary_paint_color":[
	  ""],
	 "truck_vin":[
	  "4P1CD01H57A007740"],
	 "front_axle_1_serial_number_t":[
	  "N/A"],
	 "trail_tire_serial_number_2_t":[
	  ""],
	 "desc_cab_t":[
	  "Cab, Dash-2000, 67\\" w/16\\" Raised Roof"],
	 "dealer_number_t":[
	  "2216537"],
	 "trail_tire_serial_number_1_t":[
	  ""],
	 "city_t":[
	  "Wichita"],
	 "front_weight_without_water_t":[
	  "17220"],
	 "desc_rear_tire_t":[
	  "Tires, (4) Michelin, 12R22.50 16 ply, XZY 3"],
	 "account_id":[17942,9946,17948],
	 "side_roll_protection_serial_number_t":[
	  "See Service Bulletin #189"],
	 "actual_ship_date":[
	  "2007-09-24"],
	 "alternator_serial_number":[
	  "10706"],
	 "customer_name":[
	  "WICHITA CITY OF"],
	 "transfer_case_serial_number":[
	  "N/A"],
	 "desc_generator_t":[
	  "Onan 8.0 kW Hydraulic w/ Electronic Control, Hotshift PTO"],
	 "engine_serial_number_t":[
	  "06R0924204"],
	 "alternator_serial_number_t":[
	  "10706"],
	 "country":[
	  "US"],
	 "rear_tire_serial_number_4_t":[
	  ""],
	 "desc_front_tire_t":[
	  "Tires, Michelin, 385/65R22.50 18 ply XTE2, Hiway Rib"],
	 "body_secondary_paint_color_t":[
	  ""],
	 "desc_rear_tire":[
	  "Tires, (4) Michelin, 12R22.50 16 ply, XZY 3"],
	 "pto_serial_number_t":[
	  ""],
	 "trail_tire_serial_number_3_t":[
	  ""],
	 "desc_compartment_door_t":[
	  "Doors, Roll-up, Robinson, Body"],
	 "desc_pump_t":[
	  "Pump, 2000 CSU Single Stage,Waterous"],
	 "gross_weight_without_water":[
	  "30560"],
	 "assembly_function_group_id_t":[
	  ""],
	 "desc_aerial_t":[
	  ""],
	 "drawing_number":[
	  "19437AD"],
	 "item_number":[
	  ""],
	 "rear_weight_with_water":[
	  "18000"],
	 "rear_tire_serial_number_7_t":[
	  ""],
	 "work_order_t":[
	  "07831103"],
	 "state":[
	  "KS"],
	 "work_order":[
	  "07831103"],
	 "customer_name_t":[
	  "WICHITA CITY OF"],
	 "rear_tire_serial_number_6_t":[
	  ""],
	 "tank_serial_number":[
	  "D4UPFW0809070630"],
	 "zip_t":[
	  "67203"],
	 "customer_number":[
	  "448596"],
	 "body_primary_paint_color":[
	  "#70 RED"],
	 "rear_axle_1_serial_number_t":[
	  "NKA07024407"],
	 "body_sales_option_description":[
	  "Pumper, Long, Alum, 2nd Gen"],
	 "aerial_paint_color":[
	  ""],
	 "desc_generator":[
	  "Onan 8.0 kW Hydraulic w/ Electronic Control, Hotshift PTO"],
	 "body_sales_option_description_t":[
	  "Pumper, Long, Alum, 2nd Gen"],
	 "desc_aerial":[
	  ""],
	 "warranty_start_date":[
	  "2007-09-24"],
	 "transfer_case_serial_number_t":[
	  "N/A"],
	 "zip":[
	  "67203"],
	 "transmission_serial_number":[
	  "6610233417"],
	 "machine_id_t":[
	  "36381"],
	 "gross_vehicle_weight_rating_t":[
	  ""],
	 "country_t":[
	  "US"],
	 "desc_transmission_t":[
	  "Trans, Allison Gen IV 4000 EVS PR"],
	 "pressure_governor":[
	  ""],
	 "front_weight_rating":[
	  ""],
	 "customer_number_t":[
	  "448596"],
	 "front_weight_without_water":[
	  "17220"],
	 "contract_admin_name_t":[
	  "WHITE, BRADLEY J"],
	 "front_axle_1_serial_number":[
	  "N/A"],
	 "rear_tire_serial_number_8_t":[
	  ""],
	 "address":[
	  "CITY OF WICHITA FIRE DEPARTMENT"],
	 "desc_front_axle_t":[
	  "1674997                       Axle, Front, Oshkosh TAK-4, Non Drive, 19,500 lb,"],
	 "assembly_revision_id_t":[
	  ""],
	 "front_tire_serial_number_1_t":[
	  "HAWBB5TX5006"],
	 "multiplex_serial_number":[
	  ""],
	 "front_weight_with_water":[
	  "17600"],
	 "warranty_start_date_t":[
	  "2007-09-24"],
	 "rear_weight_rating":[
	  ""]},
	{
	 "guid":"pierce/dev/my_fleet/36380",
	 "cit_client":"pierce",
	 "cit_instance":"dev",
	 "cit_domain":"my_fleet",
	 "account_id_s":"9946 17942 17948",
	 "machine_id_s":"36380",
	 "job_number_s":"19437",
	 "item_number_s":"",
	 "unit_number_s":"1",
	 "number_of_units_s":"2",
	 "work_order_s":"07831095",
	 "actual_ship_date_s":"2007-09-24",
	 "warranty_start_date_s":"2007-09-24",
	 "drawing_number_s":"19437AD",
	 "body_sales_option_description_s":"Pumper, Long, Alum, 2nd Gen",
	 "chassis_sales_option_description_s":"Dash-2000 Chassis",
	 "truck_vin_s":"4P1CD01H97A007739",
	 "truck_vin_partial_s":"7A007739",
	 "desc_cab_s":"Cab, Dash-2000, 67\\" w/16\\" Raised Roof",
	 "desc_engine_s":"S60, 470 hp, 1650 torq, w/Jake, P/N 1394129, Quantum, 2006 Allocated",
	 "desc_front_axle_s":"1674997                       Axle, Front, Oshkosh TAK-4, Non Drive, 19,500 lb,",
	 "desc_front_tire_s":"Tires, Michelin, 385/65R22.50 18 ply XTE2, Hiway Rib",
	 "desc_pump_s":"Pump, 2000 CSU Single Stage,Waterous",
	 "desc_rear_axle_s":"RS25160KFPF111                Axle, Rear, Meritor RS25-160, 27,000 lb",
	 "desc_rear_tire_s":"Tires, (4) Michelin, 12R22.50 16 ply, XZY 3",
	 "desc_tank_s":"Tank, Water, 600 Gallon, Poly, Long",
	 "desc_transmission_s":"Trans, Allison Gen IV 4000 EVS PR",
	 "desc_foam_system_s":"No Foam System Required",
	 "desc_aerial_s":"",
	 "desc_generator_s":"Onan 8.0 kW Hydraulic w/ Electronic Control, Hotshift PTO",
	 "desc_compartment_door_s":"Doors, Roll-up, Robinson, Body",
	 "pressure_governor_s":"",
	 "gross_vehicle_weight_rating_s":"",
	 "gross_weight_without_water_s":"30560",
	 "gross_weight_with_water_s":"",
	 "front_weight_rating_s":"",
	 "front_weight_without_water_s":"17220",
	 "front_weight_with_water_s":"17600",
	 "rear_weight_rating_s":"",
	 "rear_weight_without_water_s":"13340",
	 "rear_weight_with_water_s":"18000",
	 "seating_capacity_s":"5",
	 "body_primary_paint_color_s":"#70 RED",
	 "body_secondary_paint_color_s":"",
	 "aerial_paint_color_s":"",
	 "engine_serial_number_s":"06R0903232",
	 "transmission_serial_number_s":"6610233416",
	 "transfer_case_serial_number_s":"N/A",
	 "generator_serial_number_s":"F070073170",
	 "alternator_serial_number_s":"10703",
	 "pto_serial_number_s":"",
	 "pump_serial_number_s":"129301",
	 "tank_serial_number_s":"D4UPFW0809070627",
	 "aerial_serial_number_s":"",
	 "front_tire_serial_number_1_s":"HAWBB5TX4306",
	 "front_tire_serial_number_2_s":"HAWBB5TX4006",
	 "rear_tire_serial_number_1_s":"B63XPAKX1807",
	 "rear_tire_serial_number_2_s":"B63XPAKX2307",
	 "rear_tire_serial_number_3_s":"B63XPAKX2107",
	 "rear_tire_serial_number_4_s":"",
	 "rear_tire_serial_number_5_s":"",
	 "rear_tire_serial_number_6_s":"",
	 "rear_tire_serial_number_7_s":"",
	 "rear_tire_serial_number_8_s":"",
	 "trail_tire_serial_number_1_s":"",
	 "trail_tire_serial_number_2_s":"",
	 "trail_tire_serial_number_3_s":"",
	 "trail_tire_serial_number_4_s":"",
	 "side_roll_protection_serial_number_s":"See Service Bulletin #189",
	 "multiplex_serial_number_s":"",
	 "front_axle_1_serial_number_s":"N/A",
	 "front_axle_2_serial_number_s":"",
	 "rear_axle_1_serial_number_s":"NKA07023686",
	 "rear_axle_2_serial_number_s":"",
	 "salesman_name_s":"BROWN ROGER",
	 "dealer_number_s":"2216537",
	 "contract_admin_name_s":"WHITE, BRADLEY J",
	 "dealer_name_s":"CONRAD FIRE EQUIPMENT INC",
	 "customer_name_s":"WICHITA CITY OF",
	 "customer_number_s":"448596",
	 "address_s":"CITY OF WICHITA FIRE DEPARTMENT",
	 "city_s":"Wichita",
	 "state_s":"KS",
	 "zip_s":"67203",
	 "country_s":"US",
	 "chief_name_s":"KC LAWSON",
	 "manual_id_s":"",
	 "manual_revision_id_s":"",
	 "assembly_revision_id_s":"",
	 "assembly_function_group_id_s":"",
	 "cit_timestamp":"2008-02-08T19:06:12.048Z",
	 "front_axle_2_serial_number_t":[
	  ""],
	 "rear_tire_serial_number_8":[
	  ""],
	 "job_number":[
	  "19437"],
	 "rear_tire_serial_number_7":[
	  ""],
	 "chassis_sales_option_description":[
	  "Dash-2000 Chassis"],
	 "desc_pump":[
	  "Pump, 2000 CSU Single Stage,Waterous"],
	 "job_number_t":[
	  "19437"],
	 "chief_name":[
	  "KC LAWSON"],
	 "front_axle_2_serial_number":[
	  ""],
	 "contract_admin_name":[
	  "WHITE, BRADLEY J"],
	 "trail_tire_serial_number_3":[
	  ""],
	 "trail_tire_serial_number_4":[
	  ""],
	 "trail_tire_serial_number_1":[
	  ""],
	 "city":[
	  "Wichita"],
	 "trail_tire_serial_number_2":[
	  ""],
	 "rear_weight_with_water_t":[
	  "18000"],
	 "manual_id_t":[
	  ""],
	 "truck_vin_partial":[
	  "7A007739"],
	 "desc_engine_t":[
	  "S60, 470 hp, 1650 torq, w/Jake, P/N 1394129, Quantum, 2006 Allocated"],
	 "generator_serial_number":[
	  "F070073170"],
	 "number_of_units_t":[
	  "2"],
	 "rear_weight_without_water_t":[
	  "13340"],
	 "rear_tire_serial_number_1":[
	  "B63XPAKX1807"],
	 "rear_tire_serial_number_2":[
	  "B63XPAKX2307"],
	 "rear_tire_serial_number_3":[
	  "B63XPAKX2107"],
	 "rear_tire_serial_number_1_t":[
	  "B63XPAKX1807"],
	 "aerial_paint_color_t":[
	  ""],
	 "dealer_name":[
	  "CONRAD FIRE EQUIPMENT INC"],
	 "rear_tire_serial_number_4":[
	  ""],
	 "multiplex_serial_number_t":[
	  ""],
	 "rear_tire_serial_number_5":[
	  ""],
	 "rear_tire_serial_number_6":[
	  ""],
	 "desc_front_axle":[
	  "1674997                       Axle, Front, Oshkosh TAK-4, Non Drive, 19,500 lb,"],
	 "unit_number":[
	  "1"],
	 "truck_vin_partial_t":[
	  "7A007739"],
	 "tank_serial_number_t":[
	  "D4UPFW0809070627"],
	 "gross_vehicle_weight_rating":[
	  ""],
	 "desc_foam_system":[
	  "No Foam System Required"],
	 "rear_tire_serial_number_3_t":[
	  "B63XPAKX2107"],
	 "gross_weight_without_water_t":[
	  "30560"],
	 "salesman_name":[
	  "BROWN ROGER"],
	 "rear_tire_serial_number_5_t":[
	  ""],
	 "chassis_sales_option_description_t":[
	  "Dash-2000 Chassis"],
	 "desc_engine":[
	  "S60, 470 hp, 1650 torq, w/Jake, P/N 1394129, Quantum, 2006 Allocated"],
	 "pto_serial_number":[
	  ""],
	 "item_number_t":[
	  ""],
	 "gross_weight_with_water_t":[
	  ""],
	 "seating_capacity":[
	  "5"],
	 "front_tire_serial_number_2_t":[
	  "HAWBB5TX4006"],
	 "pump_serial_number":[
	  "129301"],
	 "pressure_governor_t":[
	  ""],
	 "desc_front_tire":[
	  "Tires, Michelin, 385/65R22.50 18 ply XTE2, Hiway Rib"],
	 "account_id_t":[
	  "9946 17942 17948"],
	 "number_of_units":[
	  "2"],
	 "state_t":[
	  "KS"],
	 "desc_foam_system_t":[
	  "No Foam System Required"],
	 "rear_axle_2_serial_number_t":[
	  ""],
	 "machine_id":[36380],
	 "rear_weight_without_water":[
	  "13340"],
	 "desc_rear_axle_t":[
	  "RS25160KFPF111                Axle, Rear, Meritor RS25-160, 27,000 lb"],
	 "body_primary_paint_color_t":[
	  "#70 RED"],
	 "address_t":[
	  "CITY OF WICHITA FIRE DEPARTMENT"],
	 "rear_axle_2_serial_number":[
	  ""],
	 "aerial_serial_number_t":[
	  ""],
	 "rear_tire_serial_number_2_t":[
	  "B63XPAKX2307"],
	 "front_tire_serial_number_1":[
	  "HAWBB5TX4306"],
	 "drawing_number_t":[
	  "19437AD"],
	 "front_weight_rating_t":[
	  ""],
	 "front_tire_serial_number_2":[
	  "HAWBB5TX4006"],
	 "dealer_name_t":[
	  "CONRAD FIRE EQUIPMENT INC"],
	 "salesman_name_t":[
	  "BROWN ROGER"],
	 "truck_vin_t":[
	  "4P1CD01H97A007739"],
	 "gross_weight_with_water":[
	  ""],
	 "desc_transmission":[
	  "Trans, Allison Gen IV 4000 EVS PR"],
	 "desc_tank":[
	  "Tank, Water, 600 Gallon, Poly, Long"],
	 "desc_compartment_door":[
	  "Doors, Roll-up, Robinson, Body"],
	 "transmission_serial_number_t":[
	  "6610233416"],
	 "dealer_number":[
	  "2216537"],
	 "unit_number_t":[
	  "1"],
	 "chief_name_t":[
	  "KC LAWSON"],
	 "aerial_serial_number":[
	  ""],
	 "front_weight_with_water_t":[
	  "17600"],
	 "generator_serial_number_t":[
	  "F070073170"],
	 "seating_capacity_t":[
	  "5"],
	 "side_roll_protection_serial_number":[
	  "See Service Bulletin #189"],
	 "desc_tank_t":[
	  "Tank, Water, 600 Gallon, Poly, Long"],
	 "actual_ship_date_t":[
	  "2007-09-24"],
	 "pump_serial_number_t":[
	  "129301"],
	 "desc_cab":[
	  "Cab, Dash-2000, 67\\" w/16\\" Raised Roof"],
	 "engine_serial_number":[
	  "06R0903232"],
	 "trail_tire_serial_number_4_t":[
	  ""],
	 "manual_revision_id_t":[
	  ""],
	 "rear_weight_rating_t":[
	  ""],
	 "rear_axle_1_serial_number":[
	  "NKA07023686"],
	 "desc_rear_axle":[
	  "RS25160KFPF111                Axle, Rear, Meritor RS25-160, 27,000 lb"],
	 "body_secondary_paint_color":[
	  ""],
	 "truck_vin":[
	  "4P1CD01H97A007739"],
	 "front_axle_1_serial_number_t":[
	  "N/A"],
	 "trail_tire_serial_number_2_t":[
	  ""],
	 "desc_cab_t":[
	  "Cab, Dash-2000, 67\\" w/16\\" Raised Roof"],
	 "dealer_number_t":[
	  "2216537"],
	 "trail_tire_serial_number_1_t":[
	  ""],
	 "city_t":[
	  "Wichita"],
	 "front_weight_without_water_t":[
	  "17220"],
	 "desc_rear_tire_t":[
	  "Tires, (4) Michelin, 12R22.50 16 ply, XZY 3"],
	 "account_id":[17942,9946,17948],
	 "side_roll_protection_serial_number_t":[
	  "See Service Bulletin #189"],
	 "actual_ship_date":[
	  "2007-09-24"],
	 "alternator_serial_number":[
	  "10703"],
	 "customer_name":[
	  "WICHITA CITY OF"],
	 "transfer_case_serial_number":[
	  "N/A"],
	 "desc_generator_t":[
	  "Onan 8.0 kW Hydraulic w/ Electronic Control, Hotshift PTO"],
	 "engine_serial_number_t":[
	  "06R0903232"],
	 "alternator_serial_number_t":[
	  "10703"],
	 "country":[
	  "US"],
	 "rear_tire_serial_number_4_t":[
	  ""],
	 "desc_front_tire_t":[
	  "Tires, Michelin, 385/65R22.50 18 ply XTE2, Hiway Rib"],
	 "body_secondary_paint_color_t":[
	  ""],
	 "desc_rear_tire":[
	  "Tires, (4) Michelin, 12R22.50 16 ply, XZY 3"],
	 "pto_serial_number_t":[
	  ""],
	 "trail_tire_serial_number_3_t":[
	  ""],
	 "desc_compartment_door_t":[
	  "Doors, Roll-up, Robinson, Body"],
	 "desc_pump_t":[
	  "Pump, 2000 CSU Single Stage,Waterous"],
	 "gross_weight_without_water":[
	  "30560"],
	 "assembly_function_group_id_t":[
	  ""],
	 "desc_aerial_t":[
	  ""],
	 "drawing_number":[
	  "19437AD"],
	 "item_number":[
	  ""],
	 "rear_weight_with_water":[
	  "18000"],
	 "rear_tire_serial_number_7_t":[
	  ""],
	 "work_order_t":[
	  "07831095"],
	 "state":[
	  "KS"],
	 "work_order":[
	  "07831095"],
	 "customer_name_t":[
	  "WICHITA CITY OF"],
	 "rear_tire_serial_number_6_t":[
	  ""],
	 "tank_serial_number":[
	  "D4UPFW0809070627"],
	 "zip_t":[
	  "67203"],
	 "customer_number":[
	  "448596"],
	 "body_primary_paint_color":[
	  "#70 RED"],
	 "rear_axle_1_serial_number_t":[
	  "NKA07023686"],
	 "body_sales_option_description":[
	  "Pumper, Long, Alum, 2nd Gen"],
	 "aerial_paint_color":[
	  ""],
	 "desc_generator":[
	  "Onan 8.0 kW Hydraulic w/ Electronic Control, Hotshift PTO"],
	 "body_sales_option_description_t":[
	  "Pumper, Long, Alum, 2nd Gen"],
	 "desc_aerial":[
	  ""],
	 "warranty_start_date":[
	  "2007-09-24"],
	 "transfer_case_serial_number_t":[
	  "N/A"],
	 "zip":[
	  "67203"],
	 "transmission_serial_number":[
	  "6610233416"],
	 "machine_id_t":[
	  "36380"],
	 "gross_vehicle_weight_rating_t":[
	  ""],
	 "country_t":[
	  "US"],
	 "desc_transmission_t":[
	  "Trans, Allison Gen IV 4000 EVS PR"],
	 "pressure_governor":[
	  ""],
	 "front_weight_rating":[
	  ""],
	 "customer_number_t":[
	  "448596"],
	 "front_weight_without_water":[
	  "17220"],
	 "contract_admin_name_t":[
	  "WHITE, BRADLEY J"],
	 "front_axle_1_serial_number":[
	  "N/A"],
	 "rear_tire_serial_number_8_t":[
	  ""],
	 "address":[
	  "CITY OF WICHITA FIRE DEPARTMENT"],
	 "desc_front_axle_t":[
	  "1674997                       Axle, Front, Oshkosh TAK-4, Non Drive, 19,500 lb,"],
	 "assembly_revision_id_t":[
	  ""],
	 "front_tire_serial_number_1_t":[
	  "HAWBB5TX4306"],
	 "multiplex_serial_number":[
	  ""],
	 "front_weight_with_water":[
	  "17600"],
	 "warranty_start_date_t":[
	  "2007-09-24"],
	 "rear_weight_rating":[
	  ""]},
	{
	 "guid":"pierce/dev/my_fleet/36379",
	 "cit_client":"pierce",
	 "cit_instance":"dev",
	 "cit_domain":"my_fleet",
	 "account_id_s":"66 9946",
	 "machine_id_s":"36379",
	 "job_number_s":"19436",
	 "item_number_s":"",
	 "unit_number_s":"1",
	 "number_of_units_s":"1",
	 "work_order_s":"07831085",
	 "actual_ship_date_s":"2007-09-30",
	 "warranty_start_date_s":"2007-09-30",
	 "drawing_number_s":"19436AD",
	 "body_sales_option_description_s":"Aerial, HD Ladder 105', Alum Body",
	 "chassis_sales_option_description_s":"Dash-2000 Chassis, Aerials/Tankers Tandem 48K",
	 "truck_vin_s":"4P1CD01F97A007738",
	 "truck_vin_partial_s":"7A007738",
	 "desc_cab_s":"Cab, Dash-2000, 67\\" w/10\\" Raised Roof",
	 "desc_engine_s":"C13, 525 hp, 1650 torq, w/Jake, P/N 1442621, AXT/Dash/Lance, 2006 Allocated",
	 "desc_front_axle_s":"1674999                       Axle, Front, Oshkosh TAK-4, Non Drive, 22,800 lb,",
	 "desc_front_tire_s":"Tires, Michelin, 425/65R22.50 20 ply XTE2, Hiway Rib",
	 "desc_pump_s":"Pump, S100, 2000 GPM, Single Stage, Waterous",
	 "desc_rear_axle_s":"RD23160NFK2350                Axle, Rear, Meritor RT46-160, 48,000 lb",
	 "desc_rear_tire_s":"Tires, (8) Michelin, 12R22.50 16ply, XDN2, All Season",
	 "desc_tank_s":"Tank, Water, 400 Gallon, Poly, PAL/ PAP, Notched",
	 "desc_transmission_s":"Trans, Allison Gen IV 4000 EVS P",
	 "desc_foam_system_s":"Foam Sys, Husky 12, (Dual Agent)",
	 "desc_aerial_s":"Aerial, 105' HDL, 750# Tip Load w/Waterway",
	 "desc_generator_s":"Harrison 10 kW MCR Hydraulic, Hot Shift PTO",
	 "desc_compartment_door_s":"Doors, Roll-up, Gortite, Painted and/or Locking, Side Compartments",
	 "pressure_governor_s":"",
	 "gross_vehicle_weight_rating_s":"",
	 "gross_weight_without_water_s":"59260",
	 "gross_weight_with_water_s":"",
	 "front_weight_rating_s":"",
	 "front_weight_without_water_s":"18300",
	 "front_weight_with_water_s":"18900",
	 "rear_weight_rating_s":"",
	 "rear_weight_without_water_s":"40960",
	 "rear_weight_with_water_s":"43740",
	 "seating_capacity_s":"6",
	 "body_primary_paint_color_s":"#90 RED",
	 "body_secondary_paint_color_s":"#101 BLACK",
	 "aerial_paint_color_s":"#224 SILVER MET.",
	 "engine_serial_number_s":"KCB57480",
	 "transmission_serial_number_s":"6610232871",
	 "transfer_case_serial_number_s":"N/A",
	 "generator_serial_number_s":"G278200",
	 "alternator_serial_number_s":"10666",
	 "pto_serial_number_s":"",
	 "pump_serial_number_s":"129306",
	 "tank_serial_number_s":"D4UPFW0813070403C030C020X",
	 "aerial_serial_number_s":"19436",
	 "front_tire_serial_number_1_s":"HEHXK1PX1907",
	 "front_tire_serial_number_2_s":"HEHXAJVX1407",
	 "rear_tire_serial_number_1_s":"B63X7JTX2807",
	 "rear_tire_serial_number_2_s":"",
	 "rear_tire_serial_number_3_s":"",
	 "rear_tire_serial_number_4_s":"",
	 "rear_tire_serial_number_5_s":"",
	 "rear_tire_serial_number_6_s":"",
	 "rear_tire_serial_number_7_s":"",
	 "rear_tire_serial_number_8_s":"",
	 "trail_tire_serial_number_1_s":"",
	 "trail_tire_serial_number_2_s":"",
	 "trail_tire_serial_number_3_s":"",
	 "trail_tire_serial_number_4_s":"",
	 "side_roll_protection_serial_number_s":"See Service Bulletin #189",
	 "multiplex_serial_number_s":"",
	 "front_axle_1_serial_number_s":"N/A",
	 "front_axle_2_serial_number_s":"",
	 "rear_axle_1_serial_number_s":"NKA07024382",
	 "rear_axle_2_serial_number_s":"",
	 "salesman_name_s":"OLLEY TIM",
	 "dealer_number_s":"9920699",
	 "contract_admin_name_s":"GODINA, JESSICA",
	 "dealer_name_s":"SOUTH COAST FIRE EQUIP INC",
	 "customer_name_s":"RINCON RESERVATION FIRE DEPT",
	 "customer_number_s":"1909978",
	 "address_s":"33485 VALLEY CENTER",
	 "city_s":"Valley Center",
	 "state_s":"CA",
	 "zip_s":"92082",
	 "country_s":"US",
	 "chief_name_s":"GERAD RODRIGUEZ",
	 "manual_id_s":"",
	 "manual_revision_id_s":"",
	 "assembly_revision_id_s":"",
	 "assembly_function_group_id_s":"",
	 "cit_timestamp":"2008-02-08T19:06:12.102Z",
	 "front_axle_2_serial_number_t":[
	  ""],
	 "rear_tire_serial_number_8":[
	  ""],
	 "job_number":[
	  "19436"],
	 "rear_tire_serial_number_7":[
	  ""],
	 "chassis_sales_option_description":[
	  "Dash-2000 Chassis, Aerials/Tankers Tandem 48K"],
	 "desc_pump":[
	  "Pump, S100, 2000 GPM, Single Stage, Waterous"],
	 "job_number_t":[
	  "19436"],
	 "chief_name":[
	  "GERAD RODRIGUEZ"],
	 "front_axle_2_serial_number":[
	  ""],
	 "contract_admin_name":[
	  "GODINA, JESSICA"],
	 "trail_tire_serial_number_3":[
	  ""],
	 "trail_tire_serial_number_4":[
	  ""],
	 "trail_tire_serial_number_1":[
	  ""],
	 "city":[
	  "Valley Center"],
	 "trail_tire_serial_number_2":[
	  ""],
	 "rear_weight_with_water_t":[
	  "43740"],
	 "manual_id_t":[
	  ""],
	 "truck_vin_partial":[
	  "7A007738"],
	 "desc_engine_t":[
	  "C13, 525 hp, 1650 torq, w/Jake, P/N 1442621, AXT/Dash/Lance, 2006 Allocated"],
	 "generator_serial_number":[
	  "G278200"],
	 "number_of_units_t":[
	  "1"],
	 "rear_weight_without_water_t":[
	  "40960"],
	 "rear_tire_serial_number_1":[
	  "B63X7JTX2807"],
	 "rear_tire_serial_number_2":[
	  ""],
	 "rear_tire_serial_number_3":[
	  ""],
	 "rear_tire_serial_number_1_t":[
	  "B63X7JTX2807"],
	 "aerial_paint_color_t":[
	  "#224 SILVER MET."],
	 "dealer_name":[
	  "SOUTH COAST FIRE EQUIP INC"],
	 "rear_tire_serial_number_4":[
	  ""],
	 "multiplex_serial_number_t":[
	  ""],
	 "rear_tire_serial_number_5":[
	  ""],
	 "rear_tire_serial_number_6":[
	  ""],
	 "desc_front_axle":[
	  "1674999                       Axle, Front, Oshkosh TAK-4, Non Drive, 22,800 lb,"],
	 "unit_number":[
	  "1"],
	 "truck_vin_partial_t":[
	  "7A007738"],
	 "tank_serial_number_t":[
	  "D4UPFW0813070403C030C020X"],
	 "gross_vehicle_weight_rating":[
	  ""],
	 "desc_foam_system":[
	  "Foam Sys, Husky 12, (Dual Agent)"],
	 "rear_tire_serial_number_3_t":[
	  ""],
	 "gross_weight_without_water_t":[
	  "59260"],
	 "salesman_name":[
	  "OLLEY TIM"],
	 "rear_tire_serial_number_5_t":[
	  ""],
	 "chassis_sales_option_description_t":[
	  "Dash-2000 Chassis, Aerials/Tankers Tandem 48K"],
	 "desc_engine":[
	  "C13, 525 hp, 1650 torq, w/Jake, P/N 1442621, AXT/Dash/Lance, 2006 Allocated"],
	 "pto_serial_number":[
	  ""],
	 "item_number_t":[
	  ""],
	 "gross_weight_with_water_t":[
	  ""],
	 "seating_capacity":[
	  "6"],
	 "front_tire_serial_number_2_t":[
	  "HEHXAJVX1407"],
	 "pump_serial_number":[
	  "129306"],
	 "pressure_governor_t":[
	  ""],
	 "desc_front_tire":[
	  "Tires, Michelin, 425/65R22.50 20 ply XTE2, Hiway Rib"],
	 "account_id_t":[
	  "66 9946"],
	 "number_of_units":[
	  "1"],
	 "state_t":[
	  "CA"],
	 "desc_foam_system_t":[
	  "Foam Sys, Husky 12, (Dual Agent)"],
	 "rear_axle_2_serial_number_t":[
	  ""],
	 "machine_id":[36379],
	 "rear_weight_without_water":[
	  "40960"],
	 "desc_rear_axle_t":[
	  "RD23160NFK2350                Axle, Rear, Meritor RT46-160, 48,000 lb"],
	 "body_primary_paint_color_t":[
	  "#90 RED"],
	 "address_t":[
	  "33485 VALLEY CENTER"],
	 "rear_axle_2_serial_number":[
	  ""],
	 "aerial_serial_number_t":[
	  "19436"],
	 "rear_tire_serial_number_2_t":[
	  ""],
	 "front_tire_serial_number_1":[
	  "HEHXK1PX1907"],
	 "drawing_number_t":[
	  "19436AD"],
	 "front_weight_rating_t":[
	  ""],
	 "front_tire_serial_number_2":[
	  "HEHXAJVX1407"],
	 "dealer_name_t":[
	  "SOUTH COAST FIRE EQUIP INC"],
	 "salesman_name_t":[
	  "OLLEY TIM"],
	 "truck_vin_t":[
	  "4P1CD01F97A007738"],
	 "gross_weight_with_water":[
	  ""],
	 "desc_transmission":[
	  "Trans, Allison Gen IV 4000 EVS P"],
	 "desc_tank":[
	  "Tank, Water, 400 Gallon, Poly, PAL/ PAP, Notched"],
	 "desc_compartment_door":[
	  "Doors, Roll-up, Gortite, Painted and/or Locking, Side Compartments"],
	 "transmission_serial_number_t":[
	  "6610232871"],
	 "dealer_number":[
	  "9920699"],
	 "unit_number_t":[
	  "1"],
	 "chief_name_t":[
	  "GERAD RODRIGUEZ"],
	 "aerial_serial_number":[
	  "19436"],
	 "front_weight_with_water_t":[
	  "18900"],
	 "generator_serial_number_t":[
	  "G278200"],
	 "seating_capacity_t":[
	  "6"],
	 "side_roll_protection_serial_number":[
	  "See Service Bulletin #189"],
	 "desc_tank_t":[
	  "Tank, Water, 400 Gallon, Poly, PAL/ PAP, Notched"],
	 "actual_ship_date_t":[
	  "2007-09-30"],
	 "pump_serial_number_t":[
	  "129306"],
	 "desc_cab":[
	  "Cab, Dash-2000, 67\\" w/10\\" Raised Roof"],
	 "engine_serial_number":[
	  "KCB57480"],
	 "trail_tire_serial_number_4_t":[
	  ""],
	 "manual_revision_id_t":[
	  ""],
	 "rear_weight_rating_t":[
	  ""],
	 "rear_axle_1_serial_number":[
	  "NKA07024382"],
	 "desc_rear_axle":[
	  "RD23160NFK2350                Axle, Rear, Meritor RT46-160, 48,000 lb"],
	 "body_secondary_paint_color":[
	  "#101 BLACK"],
	 "truck_vin":[
	  "4P1CD01F97A007738"],
	 "front_axle_1_serial_number_t":[
	  "N/A"],
	 "trail_tire_serial_number_2_t":[
	  ""],
	 "desc_cab_t":[
	  "Cab, Dash-2000, 67\\" w/10\\" Raised Roof"],
	 "dealer_number_t":[
	  "9920699"],
	 "trail_tire_serial_number_1_t":[
	  ""],
	 "city_t":[
	  "Valley Center"],
	 "front_weight_without_water_t":[
	  "18300"],
	 "desc_rear_tire_t":[
	  "Tires, (8) Michelin, 12R22.50 16ply, XDN2, All Season"],
	 "account_id":[66,9946],
	 "side_roll_protection_serial_number_t":[
	  "See Service Bulletin #189"],
	 "actual_ship_date":[
	  "2007-09-30"],
	 "alternator_serial_number":[
	  "10666"],
	 "customer_name":[
	  "RINCON RESERVATION FIRE DEPT"],
	 "transfer_case_serial_number":[
	  "N/A"],
	 "desc_generator_t":[
	  "Harrison 10 kW MCR Hydraulic, Hot Shift PTO"],
	 "engine_serial_number_t":[
	  "KCB57480"],
	 "alternator_serial_number_t":[
	  "10666"],
	 "country":[
	  "US"],
	 "rear_tire_serial_number_4_t":[
	  ""],
	 "desc_front_tire_t":[
	  "Tires, Michelin, 425/65R22.50 20 ply XTE2, Hiway Rib"],
	 "body_secondary_paint_color_t":[
	  "#101 BLACK"],
	 "desc_rear_tire":[
	  "Tires, (8) Michelin, 12R22.50 16ply, XDN2, All Season"],
	 "pto_serial_number_t":[
	  ""],
	 "trail_tire_serial_number_3_t":[
	  ""],
	 "desc_compartment_door_t":[
	  "Doors, Roll-up, Gortite, Painted and/or Locking, Side Compartments"],
	 "desc_pump_t":[
	  "Pump, S100, 2000 GPM, Single Stage, Waterous"],
	 "gross_weight_without_water":[
	  "59260"],
	 "assembly_function_group_id_t":[
	  ""],
	 "desc_aerial_t":[
	  "Aerial, 105' HDL, 750# Tip Load w/Waterway"],
	 "drawing_number":[
	  "19436AD"],
	 "item_number":[
	  ""],
	 "rear_weight_with_water":[
	  "43740"],
	 "rear_tire_serial_number_7_t":[
	  ""],
	 "work_order_t":[
	  "07831085"],
	 "state":[
	  "CA"],
	 "work_order":[
	  "07831085"],
	 "customer_name_t":[
	  "RINCON RESERVATION FIRE DEPT"],
	 "rear_tire_serial_number_6_t":[
	  ""],
	 "tank_serial_number":[
	  "D4UPFW0813070403C030C020X"],
	 "zip_t":[
	  "92082"],
	 "customer_number":[
	  "1909978"],
	 "body_primary_paint_color":[
	  "#90 RED"],
	 "rear_axle_1_serial_number_t":[
	  "NKA07024382"],
	 "body_sales_option_description":[
	  "Aerial, HD Ladder 105', Alum Body"],
	 "aerial_paint_color":[
	  "#224 SILVER MET."],
	 "desc_generator":[
	  "Harrison 10 kW MCR Hydraulic, Hot Shift PTO"],
	 "body_sales_option_description_t":[
	  "Aerial, HD Ladder 105', Alum Body"],
	 "desc_aerial":[
	  "Aerial, 105' HDL, 750# Tip Load w/Waterway"],
	 "warranty_start_date":[
	  "2007-09-30"],
	 "transfer_case_serial_number_t":[
	  "N/A"],
	 "zip":[
	  "92082"],
	 "transmission_serial_number":[
	  "6610232871"],
	 "machine_id_t":[
	  "36379"],
	 "gross_vehicle_weight_rating_t":[
	  ""],
	 "country_t":[
	  "US"],
	 "desc_transmission_t":[
	  "Trans, Allison Gen IV 4000 EVS P"],
	 "pressure_governor":[
	  ""],
	 "front_weight_rating":[
	  ""],
	 "customer_number_t":[
	  "1909978"],
	 "front_weight_without_water":[
	  "18300"],
	 "contract_admin_name_t":[
	  "GODINA, JESSICA"],
	 "front_axle_1_serial_number":[
	  "N/A"],
	 "rear_tire_serial_number_8_t":[
	  ""],
	 "address":[
	  "33485 VALLEY CENTER"],
	 "desc_front_axle_t":[
	  "1674999                       Axle, Front, Oshkosh TAK-4, Non Drive, 22,800 lb,"],
	 "assembly_revision_id_t":[
	  ""],
	 "front_tire_serial_number_1_t":[
	  "HEHXK1PX1907"],
	 "multiplex_serial_number":[
	  ""],
	 "front_weight_with_water":[
	  "18900"],
	 "warranty_start_date_t":[
	  "2007-09-30"],
	 "rear_weight_rating":[
	  ""]}]
 }}
JSON;
