<?
/**
* 
*/
include_once dirname(__FILE__).'/../third_party/comp_tiketcom/tiketcom_exception.php';
class Comp_tiketcom
{
	private $get_location_ep = 'http://www.tiket.com/search/autocomplete/hotel?q=';
	private $do_search_ep = 'http://www.tiket.com/search/hotel';
	private $e_key = 'asdasdasdasdabsdasdvgasdasg';
	private $_platform_img_wrap ;
	private $_tiketcom_url = 'http://www.tiket.com';
	
	function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->library('acurl');
		$this->_platform_img_wrap = 'http://assets.'.DOMAIN_BASE.'/assets/hotel/';
	}
	public function get_location($query)
	{
		$s_ori = json_decode($this->ci->acurl->simple_get($this->get_location_ep.$query), true);
		$s_final = array();
		foreach ($s_ori as $ori) {
			$build_item = array(
				'id' => $this->encrypt(element('id', $ori)),
				'name' => element('value', $ori),
				'type' => element('category', $ori),
			);
			array_push($s_final, $build_item);
		}
		if(count($s_final) > 0)
			return $s_final;
		else
			throw new Exception('Not Found', 1);
			
		
		
	}
	public function do_search($query, $option = array())
	{
		$option = (!is_array($option)) ? array() : $option;
	
	
		$opt = array(
			'q' 		=> element('query', $query),
			'startdate' => element('checkin', $query),
			'enddate' 	=> element('checkout', $query),
			'room' 		=> element('room', $query),
			'adult' 	=> element('adult', $query),
			'child' 	=> element('child', $query),
			'uid'		=> $this->decrypt(element('id_identifier', $query)),
		);
		// request OPT
	
		
		$opt = array_merge($opt, $option);
		$res =  $this->ci->acurl->simple_get($this->do_search_ep, $opt);
		
		// domize
		$html =  str_get_html($res);
		// result
		$result = array();
	
		$lists = $html->find('div[class=mainbar] ul[class=searchresult] li');
	
		if(count($lists) < 3)
			throw new Exception('Not Found', 1);
	
		foreach ($lists as $item) {
			$id = $item->getAttribute('data-id');
			if( $id <= 1 ) continue;
			// detemine promo
			$promo = $item->find('[class=itemarea] [class=itemDetail] p.promoWhat', 0);
			$promo = (!empty($promo->plaintext)) ? $promo->plaintext : null;
			
			$a_res = array(
				'id' 		=> $this->encrypt($id),
				'name' 		=> cleanup_string($item->find('[class=itemarea] [class=itemDetail] h3', 0)->plaintext),
				'img' 		=> $this->_encrypt_img_path(
					$item->find('[class=itemarea] [class=itemimg] a img', 0)->getAttribute('src')
					),
				'startfrom' => substr($item->find('[class=selectarea] span[class=currency]', 0)->getAttribute('rel'), 0, -3),
				'promo'		=> $promo,	
				'star' 		=> substr($item->find('[class=itemarea] [class=itemDetail] strong[class=ir]', 0)->plaintext, 0,1),
				'map_coor' 	=> $this->_extract_coor(
					$item->find('[class=itemarea] [class=itemDetail] a[href*=maps.google.com]', 0)->getAttribute('href')
					),
				'identifier_path' => $this->_extract_path_identifier(
					$item->find('[class=itemarea] [class=itemDetail] h3 a', 0)->getAttribute('href')
					),		
			);
			$address = $item->find('div.itemDetail', 0);
				//clean other sibling form root;
			foreach($address->find('*') as $key => $val) $address->find('*', $key)->innertext = '';
			$address = trim($address->plaintext);
			$a_res['address'] = trim($address);
			array_push($result, $a_res);
		}
		
		return $result;
		
		
	}
	public function get_detail($path_identifier, $id_encrypt, $opt = array())
	{
		// Sanitixze the opt 
			$opt = elements(
					array(
						'startdate', 
						'night', 
						'enddate', 
						'room', 
						'adult', 
						'child',
					),
					$opt,
					null
					);
			$opt['uid'] = 'business:'.$this->decrypt($id_encrypt);
			$url  = $this->_tiketcom_url.'/'.$this->decrypt($path_identifier);
			$page = str_get_html($this->ci->acurl->simple_get($url, $opt));
		
			$data = array();
			$data['id'] = $id_encrypt;
			$data['path_identifier'] = $path_identifier;
		
			$theme = (count($page->find('div[class=mainimage]')) > 0 ) ? 'mainimage' : 'no_mainimage' ;
		
			if($theme == 'mainimage'){
				$mainimage = $page->find('div[class=mainimage]', 0);
				$number_rooms = filter_var(str_replace('Number of Rooms :', '',$mainimage->find('div.detail p.room', 0)->plaintext) , FILTER_SANITIZE_NUMBER_INT);
				$price = substr($mainimage->find('div.book h3 span.currency', 0)->getAttribute('rel'), 0 , -3);
				$data['name'] 			= $mainimage->find('h2.hotelname span[itemprop=name]', 0)->plaintext;
				$data['star'] 			= substr($mainimage->find('h2.hotelname span[class*=star]', 0)->plaintext, 0,1);
				$data['address'] 		= $mainimage->find('div.detail p[class=street] span.[itemprop=address]', 0)->plaintext;
				$data['start_price'] 	= filter_var($price, FILTER_SANITIZE_NUMBER_INT);
				$data['number_of_rooms']= $number_rooms;
			}else{
				$block1 = $page->find('div.wrap div.mainbar div.block1', 0);
				$price = substr(str_replace('IDR ', '',$block1->find('div.blockright span',1)->plaintext), 0, -3);
				$num_room = filter_var(str_replace('Number of Rooms :', '',$block1->find('div.blockleft span.no span.leftab', 0)->plaintext) , FILTER_SANITIZE_NUMBER_INT);
				
				$data['name'] 			= cleanup_string($block1->find('div.blockleft h1', 0)->plaintext);
				$data['star'] 			= substr($block1->find('div.blockleft h5[class*=star]', 0)->plaintext, 0,1);
				$data['address'] 		= $block1->find('div.blockleft p.street2 span[itemprop=address]',0)->plaintext;
				$data['start_price'] 	= filter_var($price, FILTER_SANITIZE_NUMBER_INT);
				$data['number_of_rooms'] = $num_room;
			
			}
				$geo = $page->find('div.sidebarbox div.map a', 0)->getAttribute('href');
				$data['map_coordinate']= $this->_extract_coor($geo);
			
			
			if ($page->find('div[class=wrap] div[class=mainbar]',0)) {
				$wrap = $page->find('div[class=wrap] div[class=mainbar]',0);
				//echo $wrap;
				if ($wrap->find('div[class=more-less]')) {
					$description = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$wrap->find('div[class=more-less] div[class=more-block] p',0)->plaintext);
				}else{
					$description = '';
				}


				$picture = $wrap->find('div[class=photos] a');
				$resultBox = $wrap->find('div[class=searchresult] div[class=resultbox]',0);
				$class = $resultBox->find('span[itemprop=offerDetails]');
				$accorditionHotel = $wrap->find('div[class=accordion-content]',0)->find('ul',0)->find('li');
				$accorditionRoom = $wrap->find('div[class=accordion-content]',1)->find('ul',0)->find('li');
				$accorditionSport = $wrap->find('div[class=accordion-content]',2)->find('ul',0)->find('li');

				$data['description'] = $description;
				if ($wrap->find('div[class=polifees]')) {
					$policies = $wrap->find('div[class=polifees] ul',0)->find('li');
					for ($i=0; $i < count($policies); $i++) {
						$policy = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$policies[$i]->plaintext);
						$data['policies'][$i] = $policy;
					}
				}else{
					$data['policies'] = array();
				}

				for ($i=0; $i < count($picture); $i++) { 
					$pic = $picture[$i]->getAttribute('href');
					$data['pictures'][$i] = $this->_encrypt_img_path($pic);
				}

				$indexHotel = 0;
				for ($i=0; $i < count($accorditionHotel); $i++) { 
					if ($accorditionHotel[$i]->getAttribute('class')=='off') {continue;}
					$accordhotel = $accorditionHotel[$i]->plaintext;
					$data['facilities']['hotel'][$indexHotel] = $accordhotel;
					$indexHotel++;
				}
				$indexRoom = 0;
				for ($i=0; $i < count($accorditionRoom); $i++) { 
					if ($accorditionRoom[$i]->getAttribute('class')=='off') {continue;}
					$accordroom = $accorditionRoom[$i]->plaintext;
					$data['facilities']['room'][$indexRoom] = $accordroom;
					$indexRoom++;
				}
				$indexSport = 0;
				for ($i=0; $i < count($accorditionSport); $i++) { 
					if ($accorditionSport[$i]->getAttribute('class')=='off') {continue;}
					$accordsport = $accorditionSport[$i]->plaintext;
					$data['facilities']['sport'][$indexSport] = $accordsport;
					$indexSport++;
				}
				
			
				
				for ($i=0; $i < count($class); $i++) { 

					$className = $class[$i]->find('div[class=roomWrapper] div[class=roomlist] div[class=roomDesc] h4 a',0)->plaintext;
					
					$picvar = $i."_L.s.jpg";
					$classPic = $class[$i]->find('div[class=roomWrapper] div[class=roomlist] a img[class=thumb]',0)->getAttribute('src');
					$classPic = str_replace('.s.jpg', '.jpg', $classPic);
					if ($class[$i]->find('div[class=roomWrapper] div[class=rateroom] div[itemprop=availability]',0)) {
							$availability = element('1',explode(' ',
							$class[$i]->find('div[class=roomWrapper] div[class=rateroom] div[itemprop=availability]',0)->plaintext));
					}else{
						$availability = 0;
					}


					$price = str_replace('.00','',
					$class[$i]->find('div[class=roomWrapper] div[class=rateroom] h3 span[class=currency]',0)->getAttribute('rel')); 
					$roomid = $class[$i]->find('div[class=roomWrapper] div[class=roomlist] a.show_room', 0)->getAttribute('room_id');
					$data['class'][$i] = array(
						'name'		=>  trim($className),
						'price'		=>	$price,
						'discount'	=>	0,
						'picture'	=>	$this->_encrypt_img_path($classPic),
						'availability'	=>	$availability,
						'includes'	=>	'',
						'room_id'	=>	$this->encrypt($roomid),
					);
				}		
			}

		return $data;
	}
	public function do_book($query)
	{
		# code...
	}
	private function _extract_coor($url)
	{
		parse_str(element('query', parse_url($url)), $query);
		return $query['daddr'];	
	}
	private function _extract_path_identifier($url)
	{
		return encrypt(substr(element('path', parse_url($url)), 1) , $this->e_key);
	}
	private function _encrypt_img_path($url)
	{
		$path = element('path',parse_url($url));
		return $this->_platform_img_wrap.encrypt($path, $this->e_key).'.jpg';
	}
	public function _decrypt_img_path($url)
	{
		$ori_s = str_replace('.jpg', '', $url);
		return $this->_tiketcom_url.decrypt($ori_s, $this->e_key);
	}
	private function decrypt($value='')
	{
		return decrypt($value, $this->e_key);
	}
	private function encrypt($value='')
	{
		return encrypt($value, $this->e_key);
	}
	
}

