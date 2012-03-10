<?
//(START) variable untuk Fungsi Search
$conf['dosearch'] = array(
						'route_from' 		=> 'CGK',
						'route_to' 			=> 'DPS',
						'date_depart'		=> '2012-03-26',
						'adult'				=> 1,
						'child'				=> 0,
						'infant'			=> 0,
					);
//(END) variable untuk Fungsi Search


//(START) variable untuk Fungsi getDetail
$conf['getdetail'] = array(
			  			'id' 				=> '77757',
				        'company' 			=> 'SRIWIJAYA',
				        't_depart' 			=> '2012-03-26 02:05',
				        't_arrive' 			=> '2012-03-26 04:50',
				        'class' 			=> 'E',
				        'route' 			=> 'CGK,DPS',
				        't_transit_arrive' 	=> '',
				        't_transit_depart' 	=> '',
				        'price' 			=> 470000,
				        'flight_no' 		=> 'SJ 260',
						'date_depart'		=> '2012-03-26',
				        'route_from' 		=> 'CGK',
				        'route_to' 			=> 'DPS',
				        'adult' 			=> 1,
				        'infant' 			=> 0,
				        'child' 			=> 0,
				        'price_final' 		=> 0,
						'meta_data' 		=> '{"company":"SRIWIJAYA","t_depart":"2012-03-26 02:05","t_arrive":"2012-03-26 04:50","class":"E","route":"CGK,DPS","t_transit_arrive":null,"t_transit_depart":null,"price":"470000","flight_no":"SJ 260","route_from":"CGK","route_to":"DPS","adult":1,"child":0,"infant":0,"final_price":0,"arrayIndex":"1,4","radio_value":"758c7880-0903-483c-849e-e2adb702333f|fd3cdd5a-19b6-4e97-84f7-b50d0b24eede|859ff5e7-1960-444a-9f2d-4db5a96e5d38","time_depart":"2012-3-26","passangers":1}',
						
						);
//(END) variable untuk Fungsi getDetail


//(START) variable untuk Fungsi doBooking
$conf['dobooking'] = array(
	
		    'fare_data' => array(
					  			'id' 				=> '77757',
						        'company' 			=> 'SRIWIJAYA',
						        't_depart' 			=> '2012-03-26 02:05',
						        't_arrive' 			=> '2012-03-26 04:50',
						        'class' 			=> 'E',
						        'route' 			=> 'CGK,DPS',
						        't_transit_arrive' 	=> '',
						        't_transit_depart' 	=> '',
						        'price' 			=> 470000,
						        'flight_no' 		=> 'SJ 260',
								'date_depart'		=> '2012-03-26',
						        'route_from' 		=> 'CGK',
						        'route_to' 			=> 'DPS',
						        'adult' 			=> 1,
						        'infant' 			=> 0,
						        'child' 			=> 0,
						        'price_final' 		=> 0,
								'meta_data' 		=> '{"company":"SRIWIJAYA","t_depart":"2012-03-26 02:05","t_arrive":"2012-03-26 04:50","class":"E","route":"CGK,DPS","t_transit_arrive":null,"t_transit_depart":null,"price":"470000","flight_no":"SJ 260","route_from":"CGK","route_to":"DPS","adult":1,"child":0,"infant":0,"final_price":0,"arrayIndex":"1,4","radio_value":"758c7880-0903-483c-849e-e2adb702333f|fd3cdd5a-19b6-4e97-84f7-b50d0b24eede|859ff5e7-1960-444a-9f2d-4db5a96e5d38","time_depart":"2012-3-26","passangers":1}',
							),
		    'passengers_data' => array(
		            				array(
						                    'title' 	=> 'Mr',
						                    'gender' 	=> 'M',
						                    'name' 		=> 'Zidni Mubarock',
						                    'no_id' 	=> '2362863528',
						                    'type' 		=> 'adult',
											'birthday'	=> '1988-02-19'
						                )
								),

		    'contact_data' => array(
		       				    	'name' 		=> 'Zidni Mubarock',
						            'email'		=>  'zidmubarock@gmail.com',
						            'mobile' 	=> '086465765',
						            'phone' 	=> '617917791',
									'birthday'	=> '1988-02-19',
									'title'		=> 'Mr',
									'gender'	=> 'M',
									'no_id'		=> '64278364782',
						        )

		
);
//(END) variable untuk Fungsi doBooking

?>