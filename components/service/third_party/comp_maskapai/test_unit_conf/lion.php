<?
//(START) variable untuk Fungsi Search
$conf['dosearch'] = array(
						'route_from' 		=> 'CGK',
						'route_to' 			=> 'MES',
						'date_depart'		=> '2012-03-26',
						'date_return'		=> null,
						'adult'				=> 1,
						'child'				=> 0,
						'infant'			=> 0,
					);
//(END) variable untuk Fungsi Search

//(START) variable untuk Fungsi getDetail
$conf['getdetail'] = array(
            'company' => 'LION',
	        't_depart' => date('Y-m-d H:i:s',strtotime('2012-04-20 07:00:00')),
            't_arrive' => date('Y-m-d H:i:s',strtotime('2012-04-20 09:20:00')),
            'class' => 'Q',
            'route' => 'CGK,MES',
            'meta_data' => '{"seat_available":"7","flight_number_transit":"","rowID":"RM0_C2_F0","cellID":"M0_C2_F0_S14","passenger":4,"txtOutFBCsUsed":"QOW"}',
            't_transit_arrive' => '',
            't_transit_depart' => '',
            'price' => 2085700,
            'flight_no' => 'JT300',
            'created_at' => '',
            'updated_at' => '',
            'route_from' => 'CGK',
            'route_to' => 'MES',
            'adult' => '2',
            'child' => '1',
            'infant' => '1',
            'price_final' => '1',
            'price_meta' => array(
                'adult' => '',
                'infant' => '',
                'child' => ''
            			)
		        );
//(END) variable untuk Fungsi getDetail


$conf['dobook'] = array(
	'flight_data' => array(            
            'company' => 'LION',
	        't_depart' => date('Y-m-d H:i:s',strtotime('2012-04-20 07:00:00')),
            't_arrive' => date('Y-m-d H:i:s',strtotime('2012-04-20 09:20:00')),
            'class' => 'Q',
            'route' => 'CGK,MES',
            'meta_data' => '{"seat_available":"7","flight_number_transit":"","rowID":"RM0_C2_F0","cellID":"M0_C2_F0_S14","passenger":4,"txtOutFBCsUsed":"QOW"}',
            't_transit_arrive' => '',
            't_transit_depart' => '',
            'price' => '2085700',
            'flight_no' => 'JT300',
            'created_at' => '',
            'updated_at' => '',
            'route_from' => 'CGK',
            'route_to' => 'MES',
            'adult' => '2',
            'child' => '1',
            'infant' => '1',
            'price_final' => '1',
            'price_meta' => array(
                'adult' => '',
                'infant' => '',
                'child' => ''
            )
        ),
        
    'passenger_data' => array(        	
            array(
            	'name' => 'Zidni Mubarock',
            	'no_id' => '6429364294293',
            	'title' => 'Mr',
            	'gender' => 'M',
            	'birthday' => '1988-01-19',
            	'type' => 'adult'
            ),
             array(
            	'name' => 'Zidni Mubarock',
            	'no_id' => '6429136429223',
            	'title' => 'Mr',
            	'gender' => 'M',
            	'birthday' => '1987-01-19',
            	'type' => 'adult'
            ),
            array(
              	'name' => 'Zidni Mubarock',
				'no_id' => '6423164294293',
                'title' => 'Mr',
                'gender' => 'M',
                'birthday' => '2005-01-19',
                'type' => 'child'
			),
			array(
              	'name' => 'Zidni Mubarock',
				'no_id' => '6411164294293',
                'title' => 'Mr',
                'gender' => 'F',
                'birthday' => '2011-04-19',
                'type' => 'infant'
			)
        ),
                
	'contact_data' => array(
			'name' => 'Zidni Mubarock',
			'no_id' => '6429364294293',
			'title' => 'Mr',
        	'gender' => 'M',
        	'birthday' => '1988-01-19',
        	'phone' => '2342342234',
          	'mobile' => '6285697586581',
          	'email' => 'me@mail.com'
        ), 	
);
	