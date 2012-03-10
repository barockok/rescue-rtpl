<?
$conf['dosearch'] = array(
						'route_from' 		=> 'CGK',
						'route_to' 			=> 'MES',
						'date_depart'		=> '2012-03-26',
						'adult'				=> 2,
						'child'				=> 1,
						'infant'			=> 1,
					);
$conf['getdetail'] = array
				        (
							'id'		 => '77757',
				            'company'	 => 'CITILINK',
				            't_depart'	 => '2012-03-26 05:20',
				            't_arrive'	 => '2012-03-26 07:35',
				            'class'		 => 'H',
				            'route'		 => 'CGK,MES',
				            'meta_data'	 => '{"company":"CITILINK","flight_no":"GA040","t_depart":"2012-03-26 05:20","t_arrive":"2012-03-26 07:35","t_transit_depart":null,"t_transit_arrive":null,"class":"H","price":"997000","route":"CGK,MES","radio_value":"{E2FF1740-7627-4AB9-A7F4-4F5620AB6113}|{7C2454AB-5F0C-11DF-8E35-18A905E04790}||","arrayIndex":1,"time_depart":"2012-03-26","passangers":4,"adult":2,"child":1,"infant":1}',
				            't_transit_depart'	 => '',
				            't_transit_arrive'	 => '',
				            'price'	 => 997000,
				            'flight_no'	 => 'GA040',
				            'route_from' => 'CGK',
				            'route_to'	 => 'MES',
				            'adult'		 => 2,
				            'child'		 => 1,
				            'infant'	 => 1,
				            'price_final' => 0,
				            'date_depart' => '2012-03-26',
				        );
$conf['dobooking'] = Array(
	
				'fare_data'	=>	array(
					    'id'	 => 77757,
					    'company'	 => 'CITILINK',
					    't_depart'	 => '2012-03-26 05:20',
					    't_arrive'	 => '2012-03-26 07:35',
					    'class'	 => 'H',
					    'route'	 => 'CGK,MES',
					    't_transit_arrive'	 => '',
					    't_transit_depart'	 => '',
					    'price' => '3153595',
					    'flight_no'	 => 'GA040',
					    'route_to'	 => 'MES',
					    'route_from'	 => 'CGK',
					    'adult'	 => 2,
					    'child'	 => 1,
					    'infant' => 1,
					    'price_final'	 => 1,
					    'meta_data'	 => '{"id":"77757","comapny":"CITILINK","t_depart":"2012-03-26 05:20","t_arrive":"2012-03-26 07:35","class":"H","route":"CGK,MES","t_transit_arrive":false,"t_transit_depart":false,"price":3153595,"flight_no":"GA040","route_from":"CGK","route_to":"MES","adult":2,"child":1,"infant":1,"arrayIndex":1,"passangers":4,"time_depart":"2012-03-26","radio_value":"{E2FF1740-7627-4AB9-A7F4-4F5620AB6113}|{7C2454AB-5F0C-11DF-8E35-18A905E04790}||","price_meta":{"adult":1103700,"child":829525,"infant":116670}}',
					    'price_meta' => '{"adult":1103700,"child":829525,"infant":116670}',
					),
					'passengers_data' => array(
				            				array(
								                    'title' 	=> 'Mr',
								                    'gender' 	=> 'M',
								                    'name' 		=> 'Mubarock Zidni',
								                    'no_id' 	=> '2362863528',
								                    'type' 		=> 'adult',
													'birthday'	=> '1988-02-19'
								                ),
											array(
								                    'title' 	=> 'Mr',
								                    'gender' 	=> 'M',
								                    'name' 		=> 'Luthfi Hariz',
								                    'no_id' 	=> '2362863528',
								                    'type' 		=> 'adult',
													'birthday'	=> '1988-02-19'
								                ),
											array(
								                    'title' 	=> 'Mr',
								                    'gender' 	=> 'M',
								                    'name' 		=> 'Qadri Fauzan',
								                    'no_id' 	=> '2362863528',
								                    'type' 		=> 'child',
													'birthday'	=> '1988-02-19'
								                ),
											array(
								                    'title' 	=> 'Mr',
								                    'gender' 	=> 'M',
								                    'name' 		=> 'Zidni',
								                    'no_id' 	=> '2362863528',
								                    'type' 		=> 'infant',
													'birthday'	=> '1988-02-19'
								                ),
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
								        ),
);
?>