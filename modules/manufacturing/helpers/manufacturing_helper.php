<?php
defined('BASEPATH') or exit('No direct script access allowed');

	/**
	 * mrp date of week
	 * @return [type] 
	 */
	function mrp_date_of_week()
	{
		$day_of_week=[];
		$day_of_week['monday'] = 'monday';
		$day_of_week['tuesday'] = 'tuesday';
		$day_of_week['wednesday'] = 'wednesday';
		$day_of_week['thursday'] = 'thursday';
		$day_of_week['friday'] = 'friday';
		$day_of_week['saturday'] = 'saturday';
		$day_of_week['sunday'] = 'sunday';

		return $day_of_week;
	}

	/**
	 * get work center name
	 * @param  [type] $id 
	 * @return [type]     
	 */
	function get_work_center_name($id)
	{
		$CI             = &get_instance();
		$CI->db->where('id', $id);
		$CI->db->select('work_center_name');
		$work_center = $CI->db->get(db_prefix().'mrp_work_centers')->row();
		if($work_center){
			return $work_center->work_center_name; 
		}else{
			return ''; 
		} 
	}


	/**
	 * handle mrp operation attachments array
	 * @param  [type] $operation_id 
	 * @param  string $index_name   
	 * @return [type]               
	 */
	function handle_mrp_operation_attachments_array($operation_id, $index_name = 'attachments')
	{
		$uploaded_files = [];
		$path           = MANUFACTURING_OPERATION_ATTACHMENTS_UPLOAD_FOLDER.$operation_id .'/';
		$CI             = &get_instance();
			if (isset($_FILES[$index_name]['name'])
				&& ($_FILES[$index_name]['name'] != '' || is_array($_FILES[$index_name]['name']) && count($_FILES[$index_name]['name']) > 0)) {
		
				if (!is_array($_FILES[$index_name]['name'])) {
					$_FILES[$index_name]['name']     = [$_FILES[$index_name]['name']];
					$_FILES[$index_name]['type']     = [$_FILES[$index_name]['type']];
					$_FILES[$index_name]['tmp_name'] = [$_FILES[$index_name]['tmp_name']];
					$_FILES[$index_name]['error']    = [$_FILES[$index_name]['error']];
					$_FILES[$index_name]['size']     = [$_FILES[$index_name]['size']];
				}

				_file_attachments_index_fix($index_name);
				for ($i = 0; $i < count($_FILES[$index_name]['name']); $i++) {
					$tmpFilePath = $_FILES[$index_name]['tmp_name'][$i];
					if (!empty($tmpFilePath) && $tmpFilePath != '') {
						if (_perfex_upload_error($_FILES[$index_name]['error'][$i])
							|| !_upload_extension_allowed($_FILES[$index_name]['name'][$i])) {
							continue;
					}

					_maybe_create_upload_path($path);
					$filename    = unique_filename($path, $_FILES[$index_name]['name'][$i]);
					$newFilePath = $path . $filename;
					if (move_uploaded_file($tmpFilePath, $newFilePath)) {
						array_push($uploaded_files, [
							'file_name' => $filename,
							'filetype'  => $_FILES[$index_name]['type'][$i],
						]);

						$attachment   = [];
						$attachment[] = [
							'file_name' => $filename,
							'filetype'  => $_FILES[$index_name]['type'][$i],
						];

						$CI->misc_model->add_attachment_to_database($operation_id, 'mrp_operation', $attachment);

						if (is_image($newFilePath)) {
							create_img_thumb($path, $filename);
						}
					}
				}
			}
		}
		if (count($uploaded_files) > 0) {
			return $uploaded_files;
		}
		return false;
	}


	/**
	 * mrp convert accented characters
	 * @param  [type] $str 
	 * @return [type]      
	 */
	function mrp_convert_accented_characters($str)
	{

		$foreign_characters = array(
			'/ä|æ|ǽ/' => 'ae',
			'/ö|œ/' => 'oe',
			'/ü/' => 'ue',
			'/Ä/' => 'Ae',
			'/Ü/' => 'Ue',
			'/Ö/' => 'Oe',
			'/À|Á|Â|Ã|Ä|Å|Ǻ|Ā|Ă|Ą|Ǎ|Α|Ά|Ả|Ạ|Ầ|Ẫ|Ẩ|Ậ|Ằ|Ắ|Ẵ|Ẳ|Ặ|А/' => 'A',
			'/à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª|α|ά|ả|ạ|ầ|ấ|ẫ|ẩ|ậ|ằ|ắ|ẵ|ẳ|ặ|а/' => 'a',
			'/Б/' => 'B',
			'/б/' => 'b',
			'/Ç|Ć|Ĉ|Ċ|Č/' => 'C',
			'/ç|ć|ĉ|ċ|č/' => 'c',
			'/Д/' => 'D',
			'/д/' => 'd',
			'/Ð|Ď|Đ|Δ/' => 'Dj',
			'/ð|ď|đ|δ/' => 'dj',
			'/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě|Ε|Έ|Ẽ|Ẻ|Ẹ|Ề|Ế|Ễ|Ể|Ệ|Е|Э/' => 'E',
			'/è|é|ê|ë|ē|ĕ|ė|ę|ě|έ|ε|ẽ|ẻ|ẹ|ề|ế|ễ|ể|ệ|е|э/' => 'e',
			'/Ф/' => 'F',
			'/ф/' => 'f',
			'/Ĝ|Ğ|Ġ|Ģ|Γ|Г|Ґ/' => 'G',
			'/ĝ|ğ|ġ|ģ|γ|г|ґ/' => 'g',
			'/Ĥ|Ħ/' => 'H',
			'/ĥ|ħ/' => 'h',
			'/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ|Η|Ή|Ί|Ι|Ϊ|Ỉ|Ị|И|Ы/' => 'I',
			'/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı|η|ή|ί|ι|ϊ|ỉ|ị|и|ы|ї/' => 'i',
			'/Ĵ/' => 'J',
			'/ĵ/' => 'j',
			'/Ķ|Κ|К/' => 'K',
			'/ķ|κ|к/' => 'k',
			'/Ĺ|Ļ|Ľ|Ŀ|Ł|Λ|Л/' => 'L',
			'/ĺ|ļ|ľ|ŀ|ł|λ|л/' => 'l',
			'/М/' => 'M',
			'/м/' => 'm',
			'/Ñ|Ń|Ņ|Ň|Ν|Н/' => 'N',
			'/ñ|ń|ņ|ň|ŉ|ν|н/' => 'n',
			'/Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ|Ο|Ό|Ω|Ώ|Ỏ|Ọ|Ồ|Ố|Ỗ|Ổ|Ộ|Ờ|Ớ|Ỡ|Ở|Ợ|О/' => 'O',
			'/ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º|ο|ό|ω|ώ|ỏ|ọ|ồ|ố|ỗ|ổ|ộ|ờ|ớ|ỡ|ở|ợ|о/' => 'o',
			'/П/' => 'P',
			'/п/' => 'p',
			'/Ŕ|Ŗ|Ř|Ρ|Р/' => 'R',
			'/ŕ|ŗ|ř|ρ|р/' => 'r',
			'/Ś|Ŝ|Ş|Ș|Š|Σ|С/' => 'S',
			'/ś|ŝ|ş|ș|š|ſ|σ|ς|с/' => 's',
			'/Ț|Ţ|Ť|Ŧ|τ|Т/' => 'T',
			'/ț|ţ|ť|ŧ|т/' => 't',
			'/Þ|þ/' => 'th',
			'/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ|Ũ|Ủ|Ụ|Ừ|Ứ|Ữ|Ử|Ự|У/' => 'U',
			'/ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ|υ|ύ|ϋ|ủ|ụ|ừ|ứ|ữ|ử|ự|у/' => 'u',
			'/Ƴ|Ɏ|Ỵ|Ẏ|Ӳ|Ӯ|Ў|Ý|Ÿ|Ŷ|Υ|Ύ|Ϋ|Ỳ|Ỹ|Ỷ|Ỵ|Й/' => 'Y',
			'/ẙ|ʏ|ƴ|ɏ|ỵ|ẏ|ӳ|ӯ|ў|ý|ÿ|ŷ|ỳ|ỹ|ỷ|ỵ|й/' => 'y',
			'/В/' => 'V',
			'/в/' => 'v',
			'/Ŵ/' => 'W',
			'/ŵ/' => 'w',
			'/Ź|Ż|Ž|Ζ|З/' => 'Z',
			'/ź|ż|ž|ζ|з/' => 'z',
			'/Æ|Ǽ/' => 'AE',
			'/ß/' => 'ss',
			'/Ĳ/' => 'IJ',
			'/ĳ/' => 'ij',
			'/Œ/' => 'OE',
			'/ƒ/' => 'f',
			'/ξ/' => 'ks',
			'/π/' => 'p',
			'/β/' => 'v',
			'/μ/' => 'm',
			'/ψ/' => 'ps',
			'/Ё/' => 'Yo',
			'/ё/' => 'yo',
			'/Є/' => 'Ye',
			'/є/' => 'ye',
			'/Ї/' => 'Yi',
			'/Ж/' => 'Zh',
			'/ж/' => 'zh',
			'/Х/' => 'Kh',
			'/х/' => 'kh',
			'/Ц/' => 'Ts',
			'/ц/' => 'ts',
			'/Ч/' => 'Ch',
			'/ч/' => 'ch',
			'/Ш/' => 'Sh',
			'/ш/' => 'sh',
			'/Щ/' => 'Shch',
			'/щ/' => 'shch',
			'/Ъ|ъ|Ь|ь/' => '',
			'/Ю/' => 'Yu',
			'/ю/' => 'yu',
			'/Я/' => 'Ya',
			'/я/' => 'ya'
		);

	
			$array_from = array_keys($foreign_characters);
			$array_to = array_values($foreign_characters);

		return preg_replace($array_from, $array_to, $str);
	}

	/**
	 * get category name
	 * @param  [type] $id 
	 * @return [type]     
	 */
	function get_category_name($id)
	{
		$CI             = &get_instance();
		$CI->db->where('id', $id);
		$CI->db->select('category_name');
		$category = $CI->db->get(db_prefix().'mrp_unit_measure_categories')->row();
		if($category){
			return $category->category_name; 
		}else{
			return ''; 
		} 
	}

	/**
	 * mrp get taxes
	 * @param  string $id 
	 * @return [type]     
	 */
	function mrp_get_taxes($id ='')
	{
		$CI           = & get_instance();

		if (is_numeric($id)) {
			$CI->db->where('id',$id);

			return $CI->db->get(db_prefix().'taxes')->row();
		}
		$CI->db->order_by('taxrate', 'ASC');
		return $CI->db->get(db_prefix().'taxes')->result_array();

	}

	/**
	 * mrp generate commodity barcode
	 * @return [type] 
	 */
	function mrp_generate_commodity_barcode()
	{
		$CI           = & get_instance();

		$item = false;
		do {
			$length = 11;
			$chars = '0123456789';
			$count = new_strlen($chars);
			$password = '';
			for ($i = 0; $i < $length; $i++) {
				$index = rand(0, $count - 1);
				$password .= mb_substr($chars, $index, 1);
			}
			$CI->db->where('commodity_barcode', $password);
			$item = $CI->db->get(db_prefix() . 'items')->row();
		} while ($item);

		return $password;
	}


	/**
	 * mrp handle product attachments
	 * @param  [type] $id 
	 * @return [type]     
	 */
	function mrp_handle_product_attachments($id)
	{

		if (isset($_FILES['file']) && _perfex_upload_error($_FILES['file']['error'])) {
			header('HTTP/1.0 400 Bad error');
			echo _perfex_upload_error($_FILES['file']['error']);
			die;
		}
		$path = MANUFACTURING_PRODUCT_UPLOAD . $id . '/';
		$CI   = & get_instance();

		if (isset($_FILES['file']['name'])) {

        // Get the temp file path
			$tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
			if (!empty($tmpFilePath) && $tmpFilePath != '') {

				_maybe_create_upload_path($path);
				$filename    = $_FILES['file']['name'];
				$newFilePath = $path . $filename;
            // Upload the file into the temp dir
				if (move_uploaded_file($tmpFilePath, $newFilePath)) {

					$attachment   = [];
					$attachment[] = [
						'file_name' => $filename,
						'filetype'  => $_FILES['file']['type'],
					];

					$CI->misc_model->add_attachment_to_database($id, 'commodity_item_file', $attachment);

				}
			}
		}

	}


	/**
	 * mrp get product
	 * @param  [type] $id 
	 * @return [type]     
	 */
	function mrp_get_product($id)
	{
		$CI   = & get_instance();
		if (is_numeric($id)) {
			$CI->db->where('id', $id);

			return $CI->db->get(db_prefix() . 'items')->row();
		}
		if ($id == false) {
			return $CI->db->query('select * from '.db_prefix().'items')->result_array();
		}
	}


	/**
	 * get product name
	 * @param  [type] $id 
	 * @return [type]     
	 */
	function mrp_get_product_name($id)
	{
	    $CI   = & get_instance();
	    $CI->db->where('id', $id);
	    $product = $CI->db->get(db_prefix() . 'items')->row();

	    $name='';
	    if($product){
	    	if(new_strlen($product->commodity_code) > 0){
	    		$name .= $product->commodity_code.'_'.$product->description;
	    	}else{
	    		$name .= $product->description;
	    	}
	    }

	    return $name;
	}


	/**
	 * mrp get unit name
	 * @param  [type] $unit 
	 * @return [type]       
	 */
	function mrp_get_unit_name($id)
	{
		$CI   = & get_instance();
	    $CI->db->where('unit_type_id', $id);
	    $unit = $CI->db->get(db_prefix() . 'ware_unit_type')->row();

	    $name='';
	    if($unit){
	    	$name .= $unit->unit_name;
	    }

	    return $name;
	}


	/**
	 * mrp get routing name
	 * @param  [type] $id 
	 * @return [type]     
	 */
	function mrp_get_routing_name($id)
	{
		$CI   = & get_instance();
	    $CI->db->where('id', $id);
	    $routing = $CI->db->get(db_prefix() . 'mrp_routings')->row();

	    $name='';
	    if($routing){
	    	$name .= $routing->routing_name;
	    }

	    return $name;
	}


	/**
	 * mrp get routing detail name
	 * @param  [type] $id 
	 * @return [type]     
	 */
	function mrp_get_routing_detail_name($id)
	{
		$CI   = & get_instance();
	    $CI->db->where('id', $id);
	    $operation = $CI->db->get(db_prefix() . 'mrp_routing_details')->row();

	    $name='';
	    if($operation){
	    	$name .= $operation->operation;
	    }

	    return $name;
	}


	/**
	 * mrp get bill of material
	 * @param  [type] $id 
	 * @return [type]     
	 */
	function mrp_get_bill_of_material($id)
	{
		$CI   = & get_instance();
	    $CI->db->where('id', $id);
	    $bill_of_material = $CI->db->get(db_prefix() . 'mrp_bill_of_materials')->row();

	    $name='';
	    if($bill_of_material){
	    	$name .= $bill_of_material->product_id;
	    }

	    return $name;
	}

	/**
	 * mrp payroll get status modules
	 * @param  [type] $module_name 
	 * @return [type]              
	 */
	function mrp_get_status_modules($module_name){
		$CI             = &get_instance();

		$sql = 'select * from '.db_prefix().'modules where module_name = "'.$module_name.'" AND active =1 ';
		$module = $CI->db->query($sql)->row();
		if($module){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * mrp get warehouse name
	 * @param  [type] $id 
	 * @return [type]     
	 */
	function mrp_get_warehouse_name($id)
	{
		$CI           = & get_instance();

		$warehouse_name='';

		$CI->db->where('warehouse_id', $id);
		$warehouse = $CI->db->get(db_prefix() . 'warehouse')->row();
		if($warehouse){
			$warehouse_name .= $warehouse->warehouse_name;
		}

		return $warehouse_name;

	}

	/**
	 * mrp get manufacturing code
	 * @param  [type] $id 
	 * @return [type]     
	 */
	function mrp_get_manufacturing_code($id)
	{
	    $CI   = & get_instance();
	    $CI->db->where('id', $id);
	    $product = $CI->db->get(db_prefix() . 'mrp_manufacturing_orders')->row();

	    $manufacturing_order_code='';
	    if($product){
	    	$manufacturing_order_code .= $product->manufacturing_order_code;
	    }

	    return $manufacturing_order_code;
	}

	/**
	 * mrp product type
	 * @return [type] 
	 */
	function mrp_product_type()
	{
		$array_product_type=[];
		
		$array_product_type[] = [
			'name' => 'consumable',
			'label' => _l('mrp_consumable'),
		];
		$array_product_type[] = [
			'name' => 'service',
			'label' => _l('mrp_service'),
		];
		$array_product_type[] = [
			'name' => 'storable_product',
			'label' => _l('mrp_storable_product'),
		];

		return $array_product_type;
	}

	/**
	 * mrp purchase request code
	 * @param  [type] $pur_id 
	 * @return [type]         
	 */
	function mrp_purchase_request_code($pur_id)
	{
		$CI   = & get_instance();
	    $CI->db->where('id', $pur_id);
	    $pur = $CI->db->get(db_prefix() . 'pur_request')->row();

	    $name='';
	    if($pur){
	    	$name .= $pur->pur_rq_code;
	    }

	    return $name;
	}

	/**
	 * working hour sample data
	 * @return [type] 
	 */
	function working_hour_sample_data()
	{
		$sample_data=[];

		//monday
		$sample_data[] = [
			'working_hour_name' => _l('Monday_Morning'),
			'day_of_week' => 'monday',
			'day_period' => 'morning',
			'work_from' => '08:00',
			'work_to' => '12:00',
		];

		$sample_data[] = [
			'working_hour_name' => _l('Monday_Afternoon'),
			'day_of_week' => 'monday',
			'day_period' => 'afternoon',
			'work_from' => '13:00',
			'work_to' => '17:00',
		];

		//tuesday
		$sample_data[] = [
			'working_hour_name' => _l('Tuesday_Morning'),
			'day_of_week' => 'tuesday',
			'day_period' => 'morning',
			'work_from' => '08:00',
			'work_to' => '12:00',
		];

		$sample_data[] = [
			'working_hour_name' => _l('Tuesday_Afternoon'),
			'day_of_week' => 'tuesday',
			'day_period' => 'afternoon',
			'work_from' => '13:00',
			'work_to' => '17:00',
		];

		//webnesday
		$sample_data[] = [
			'working_hour_name' => _l('Wednesday_Morning'),
			'day_of_week' => 'wednesday',
			'day_period' => 'morning',
			'work_from' => '08:00',
			'work_to' => '12:00',
		];

		$sample_data[] = [
			'working_hour_name' => _l('Wednesday_Afternoon'),
			'day_of_week' => 'wednesday',
			'day_period' => 'afternoon',
			'work_from' => '13:00',
			'work_to' => '17:00',
		];

		//Thursday
		$sample_data[] = [
			'working_hour_name' => _l('Thursday_Morning'),
			'day_of_week' => 'thursday',
			'day_period' => 'morning',
			'work_from' => '08:00',
			'work_to' => '12:00',
		];

		$sample_data[] = [
			'working_hour_name' => _l('Thursday_Afternoon'),
			'day_of_week' => 'thursday',
			'day_period' => 'afternoon',
			'work_from' => '13:00',
			'work_to' => '17:00',
		];

		//Friday
		$sample_data[] = [
			'working_hour_name' => _l('Friday_Morning'),
			'day_of_week' => 'friday',
			'day_period' => 'morning',
			'work_from' => '08:00',
			'work_to' => '12:00',
		];

		$sample_data[] = [
			'working_hour_name' => _l('Friday_Afternoon'),
			'day_of_week' => 'friday',
			'day_period' => 'afternoon',
			'work_from' => '13:00',
			'work_to' => '17:00',
		];
		
		return $sample_data;
	}

	/**
	 * mrp required inventory purchase module
	 * @return [type] 
	 */
	function mrp_required_inventory_purchase_module()
	{	
		$CI   = & get_instance();

		//required inventory module version 1.1.8
		$sql = 'select * from '.db_prefix().'modules where module_name = "warehouse" AND active =1 ';
		$module = $CI->db->query($sql)->row();
		if($module){
			if(version_compare('1.1.8', $module->installed_version, '<=')){
				$inventory = true;
			}else{
				$inventory = false;
			}
		}else{
			$inventory = false;
		}

		//required purchase module
		$purchase = mrp_get_status_modules('purchase');

		$data=[];
		$data['inventory'] = $inventory;
		$data['purchase'] = $purchase;

		return $data;
	}

	/**
	 * get mrp option
	 * @param  [type] $name 
	 * @return [type]       
	 */
	function get_mrp_option($name)
	{
		$CI = & get_instance();
		$options = [];
		$val  = '';
		$name = trim($name);
		if (!isset($options[$name])) {
			$CI->db->select('option_val');
			$CI->db->where('option_name', $name);
			$row = $CI->db->get(db_prefix() . 'mrp_option')->row();
			if ($row) {
				$val = $row->option_val;
			}
		} else {
			$val = $options[$name];
		}
		return hooks()->apply_filters('get_mrp_option', $val, $name);
	}

	/**
	 * mrp row options exists
	 * @param  [type] $name 
	 * @return [type]       
	 */
	function mrp_row_options_exists($name){
		$CI = & get_instance();
		$i = count($CI->db->query('Select * from '.db_prefix().'mrp_option where option_name = '.$name)->result_array());
		if($i == 0){
			return 0;
		}
		if($i > 0){
			return 1;
		}
	}

	/**
	 * mrp get bill of material code
	 * @param  [type] $id 
	 * @return [type]     
	 */
	function mrp_get_bill_of_material_code($id)
	{
		$CI   = & get_instance();
	    $CI->db->where('id', $id);
	    $bill_of_material = $CI->db->get(db_prefix() . 'mrp_bill_of_materials')->row();

	    $name='';
	    if($bill_of_material){
	    	$name .= $bill_of_material->bom_code;
	    }

	    return $name;
	}

	/**
	 * mo get commodity name
	 * @param  [type] $id 
	 * @return [type]     
	 */
	function mo_get_commodity_name($id)
	{
		$item_name = '';
		$CI           = & get_instance();

		$CI->db->where('id', $id);
		$item = $CI->db->get(db_prefix() . 'items')->row();
		if($item){
			$item_name .= $item->commodity_code.'_'.$item->description;
		}
		
	}

	/**
	 * [new_html_entity_decode description]
	 * @param  [type] $str [description]
	 * @return [type]      [description]
	 */
	if (!function_exists('new_html_entity_decode')) {
		
		function new_html_entity_decode($str){
			return html_entity_decode($str ?? '');
		}
	}

	if (!function_exists('new_strlen')) {
		
		function new_strlen($str){
			return strlen($str ?? '');
		}
	}

	if (!function_exists('new_str_replace')) {
		
		function new_str_replace($search, $replace, $subject){
			return str_replace($search, $replace, $subject ?? '');
		}
	}

	if (!function_exists('new_explode')) {
		
		function new_explode($delimiter, $string){
			return explode($delimiter, $string ?? '');
		}
	}

	/**
	 * mrp bom change log types
	 * @return [type] 
	 */
	function mrp_bom_change_log_types()
	{
		$change_types=[];

		//1
		$change_types[] = [
			'name' => 'Component_additions_to_the_assembly',
			'label' => _l('Component_additions_to_the_assembly'),
		];
		//2
		$change_types[] = [
			'name' => 'Component_deletion_from_the_assembly',
			'label' => _l('Component_deletion_from_the_assembly'),
		];
		//3
		$change_types[] = [
			'name' => 'Component_quantity_change_during_edit',
			'label' => _l('Component_quantity_change_during_edit'),
		];
		//4
		$change_types[] = [
			'name' => 'Component_engineering_issue_change_without_quantity_change',
			'label' => _l('Component_engineering_issue_change_without_quantity_change'),
		];
		//5
		$change_types[] = [
			'name' => 'is_use',
			'label' => _l('is_use'),
		];
		//6
		$change_types[] = [
			'name' => 'mrp_Other',
			'label' => _l('mrp_Other'),
		];
		//6
		$change_types[] = [
			'name' => 'mrp_change_quantity',
			'label' => _l('mrp_change_quantity'),
		];
		
		return $change_types;
	}

	/**
	 * get work order name
	 * @param  [type] $id 
	 * @return [type]     
	 */
	function get_work_order_name($id)
	{
		$CI             = &get_instance();
		$CI->db->where('id', $id);
		$CI->db->select('operation_name');
		$work_order = $CI->db->get(db_prefix().'mrp_work_orders')->row();
		if($work_order){
			return $work_order->operation_name; 
		}else{
			return ''; 
		} 
	}