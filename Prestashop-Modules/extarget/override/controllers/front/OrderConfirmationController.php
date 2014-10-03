<?php

if (!defined('_PS_VERSION_'))
  exit;

class OrderConfirmationController extends OrderConfirmationControllerCore
{


public function init()
	{
		parent::init();

		$this->id_cart = (int)(Tools::getValue('id_cart', 0));
		$is_guest = false;

		/* check if the cart has been made by a Guest customer, for redirect link */
		if (Cart::isGuestCartByCartId($this->id_cart))
		{
			$is_guest = true;
			$redirectLink = 'index.php?controller=guest-tracking';
		}
		else
			$redirectLink = 'index.php?controller=history';

		$this->id_module = (int)(Tools::getValue('id_module', 0));
		$this->id_order = Order::getOrderByCartId((int)($this->id_cart));
		$this->secure_key = Tools::getValue('key', false);
		$order = new Order((int)($this->id_order));
		if ($is_guest)
		{
			$customer = new Customer((int)$order->id_customer);
			$redirectLink .= '&id_order='.$order->reference.'&email='.urlencode($customer->email);
		}
		if (!$this->id_order || !$this->id_module || !$this->secure_key || empty($this->secure_key))
			Tools::redirect($redirectLink.(Tools::isSubmit('slowvalidation') ? '&slowvalidation' : ''));
		$this->reference = $order->reference;
		if (!Validate::isLoadedObject($order) || $order->id_customer != $this->context->customer->id || $this->secure_key != $order->secure_key)
			Tools::redirect($redirectLink);
		$module = Module::getInstanceById((int)($this->id_module));
		if ($order->payment != $module->displayName)
			Tools::redirect($redirectLink);
	}

	/**
	 * Assign template vars related to page content
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();

		$this->context->smarty->assign(array(
			'is_guest' => $this->context->customer->is_guest,
			'HOOK_ORDER_CONFIRMATION' => $this->displayOrderConfirmation(),
			'HOOK_PAYMENT_RETURN' => $this->displayPaymentReturn(),
			'PHP_ERRORS' => $this->php_errors
		));

		if ($this->context->customer->is_guest)
		{
			$this->context->smarty->assign(array(
				'id_order' => $this->id_order,
				'reference_order' => $this->reference,
				'id_order_formatted' => sprintf('#%06d', $this->id_order),
				'email' => $this->context->customer->email
			));
			/* If guest we clear the cookie for security reason */
			$this->context->customer->mylogout();
		}

		$this->extractData();
	    $this->order_confirmation_TS_ET();
		$this->setTemplate(_PS_THEME_DIR_.'order-confirmation.tpl');
	}

	/**
	 * Execute the hook displayPaymentReturn
	 */
	public function displayPaymentReturn()
	{
		if (Validate::isUnsignedId($this->id_order) && Validate::isUnsignedId($this->id_module))
		{
			$params = array();
			$order = new Order($this->id_order);
			$currency = new Currency($order->id_currency);

			if (Validate::isLoadedObject($order))
			{
				$params['total_to_pay'] = $order->getOrdersTotalPaid();
				$params['currency'] = $currency->sign;
				$params['objOrder'] = $order;
				$params['currencyObj'] = $currency;

				return Hook::exec('displayPaymentReturn', $params, $this->id_module);
			}
		}
		return false;
	}

	/**
	 * Execute the hook displayOrderConfirmation
	 */
	public function displayOrderConfirmation()
	{
		if (Validate::isUnsignedId($this->id_order))
		{
			$params = array();
			$order = new Order($this->id_order);
			$currency = new Currency($order->id_currency);

			if (Validate::isLoadedObject($order))
			{
				$params['total_to_pay'] = $order->getOrdersTotalPaid();
				$params['currency'] = $currency->sign;
				$params['objOrder'] = $order;
				$params['currencyObj'] = $currency;

				return Hook::exec('displayOrderConfirmation', $params);
			}
		}
		return false;
	}


public function extractData() 
  {

    $sql = 'SELECT * FROM '._DB_PREFIX_.'order_detail WHERE id_order =  "'.$this->id_order.'"';
    $results = Db::getInstance()->ExecuteS($sql);
   	$records = count($results);

   	for ($i = 0; $i < $records; $i++)
   	{

   			$sql = 'SELECT * FROM '._DB_PREFIX_.'order_detail WHERE id_order =  "'.$this->id_order.'"';
    $results = Db::getInstance()->ExecuteS($sql);

   				$this->counter = $i;
 
                $orderID = $results[$i]['id_order'];
                $itemNo = $results[$i]['product_id'];
                $UOMprice = $results[$i]['product_price'];
                $Qty = $results[$i]['product_quantity'];
          

            $subtotal = $Qty * $UOMprice;



      $sql = 'SELECT * FROM '._DB_PREFIX_.'product_lang WHERE id_product = "'.$itemNo.'"';
      $results = Db::getInstance()->ExecuteS($sql);
        foreach ($results as $row)
        {
          $shortDesc = $row['description_short'];
        }
 

      $sql = 'SELECT * FROM '._DB_PREFIX_.'orders WHERE id_order = "'.$orderID.'"';
      $results = Db::getInstance()->ExecuteS($sql);
        foreach ($results as $row)
        {
          $customerID = $row['id_customer'];
          $tempdate_add = $row['date_add'];
          $totalpaid = $row['total_paid_tax_incl'];
          $totaltaxexcl = $row['total_paid_tax_excl'];
          $shipping = $row['total_shipping'];
          $paymenttype = $row['payment'];
          $totalproduct = $row['total_products'];

        }

      $sql = 'SELECT * FROM '._DB_PREFIX_.'customer WHERE id_customer = "'.$customerID.'"';
      $results = Db::getInstance()->ExecuteS($sql);
        foreach ($results as $row)
        {
          $firstName = $row['firstname'];
          $lastName = $row['lastname'];
          $email = $row['email'];
        }

      $sql = 'SELECT * FROM '._DB_PREFIX_.'cart WHERE id_customer = "'.$customerID.'"';
      $results = Db::getInstance()->ExecuteS($sql);
        foreach ($results as $row)
        {
          $deliveryAddressPointer = $row['id_address_delivery'];
          $billingAddressPointer = $row['id_address_invoice'];
        }

      $sql = 'SELECT * FROM '._DB_PREFIX_.'address WHERE id_address = "'.$deliveryAddressPointer.'"';
      $results = Db::getInstance()->ExecuteS($sql);
        foreach ($results as $row)
        {
          $firstNameS = $row['firstname'];
          $lastNameS = $row['lastname'];
          $address1S = $row['address1'];
          $address2S = $row['address2'];
          $zipS = $row['postcode'];
          $cityS = $row['city'];
          $tempstateS = $row['id_state'];         
        }

      $sql = 'SELECT * FROM '._DB_PREFIX_.'address WHERE id_address = "'.$billingAddressPointer.'"';
      $results = Db::getInstance()->ExecuteS($sql);
        foreach ($results as $row)
        {
          $firstNameB = $row['firstname'];
          $lastNameB = $row['lastname'];
          $address1B = $row['address1'];
          $address2B = $row['address2'];
          $zipB = $row['postcode'];
          $cityB = $row['city'];
          $tempstateB = $row['id_state'];         
        }


      $sql = 'SELECT * FROM '._DB_PREFIX_.'state WHERE id_state = "'.$tempstateS.'"';
      $results = Db::getInstance()->ExecuteS($sql);
      	foreach ($results as $row)
      	{
      		$stateS = $row['iso_code'];
      	}

      $sql = 'SELECT * FROM '._DB_PREFIX_.'state WHERE id_state = "'.$tempstateB.'"';
      $results = Db::getInstance()->ExecuteS($sql);
      	foreach ($results as $row)
      	{
      		$stateB = $row['iso_code'];
      	}	

      /*   Previously, newID was generated with the CF CreateUUID() function.  
		   There is no such function in PHP, but one close is uniquid().  Using this
		   and applying an md5() hash to it and some processing gets us a UUID in the
		   required 8-4-4-16 format.
	  */

      $tempID = md5(uniqid('', true));

      $this->newID = substr($tempID, 0, 8) .'-'.
	  substr($tempID, 8, 4) .'-'.
	  substr($tempID, 12, 4) .'-'.
	  substr($tempID, 16);



	  $month = substr($tempdate_add, 5, 2);
	  $day = substr($tempdate_add, 8, 2);
	  $year = substr($tempdate_add, 0, 4);

	  $this->date_add = $month . '/' . $day . '/' . $year;

	  $temptime = substr($tempdate_add, 11, 8);
	  $temphour = floatval(substr($temptime, 0, 2));

	  $minute =substr($temptime, 3, 2);


	  if ($temphour == 12 && $minute == "00" || ($temphour > 12 && $temphour != 24))
	  {
	  	$period = "PM";
	  }

	  if ($temphour == 24 && $minute == "00" || $temphour < 12)
	  {
	  	$period = "AM";
	  }

	  if ($temphour > 12)
	  {
	  	$hour = $temphour - 12;
	  }

	  $this->orderTime = $hour . ':' . $minute . ' ' . $period;


	  $this->totalproduct = $totalproduct;
	  $this->total = $totalpaid;
	  $this->tax = floatval($totalpaid) - floatval($totaltaxexcl);
	  $this->shipping = $shipping;
	  $this->payment = $paymenttype;

      $this->orderID = $orderID;
      $this->email = $email;
      $this->itemNo = $itemNo;
      $this->UOMprice = $UOMprice;
      $this->Qty = $Qty;
      $this->subtotal = $subtotal;
      $this->shortDesc = substr($shortDesc, 0, 98) . '..';
      

      $this->firstNameS = $firstNameS;
      $this->lastNameS = $lastNameS;
      $this->address1S = $address1S;
      $this->address2S = $address2S;
      $this->cityS = $cityS;
      $this->stateS = $stateS;
      $this->zipS = $zipS;


      $this->firstNameB = $firstNameB;
      $this->lastNameB = $lastNameB;
      $this->address1B = $address1B;
      $this->address2B = $address2B;
      $this->cityB = $cityB;
      $this->stateB = $stateB;
      $this->zipB = $zipB;


      $this->order_details_ET();
  	  }



  }


public function order_details_ET() {
if ($this->counter < 1)
{
require("exacttarget_soap_client.php");

	/* set the endpoint, notice that the account is on the s6 instance */
	$this->wsdl								= "https://webservice.s6.exacttarget.com/etframework.wsdl";
}
	try {


		/* Create the Soap Client */
		$client[$this->counter]						= new ExactTargetSoapClient($this->wsdl, array('trace'=>1));


		/* Set username and password here */
		$client[$this->counter]->username			= "############"; // add your API username here
		$client[$this->counter]->password			= "############"; // add your API username here

	
		/* ExactTarget_DataExtensionObject */
		$de							= new ExactTarget_DataExtensionObject();
		$de->CustomerKey			= "OrderConfirmationDetails"; //external key for the data extension


		/* ExactTarget_APIProperties - one for each field */
		/* The values for each field are a string, you may need to perform data type conversions */
		/* If a field is null then there are two options:  Either pass through an empty string or adjust the logic in the code to not create the property and do not add it to the properties array.  Passing through a value of null will cause the API call to fail. */
		$val1key						= new ExactTarget_APIProperty();
		$val1key->Name				= "SubscriberKey"; // name of DE field
		$val1key->Value			= "#############"; // value for DE field

		$val2key						= new ExactTarget_APIProperty();
		$val2key->Name				= "EmailAddress";
		$val2key->Value			= $this->email;

		$val3key						= new ExactTarget_APIProperty();
		$val3key->Name				= "OrderID";
		$val3key->Value			= "32";

		$val4key						= new ExactTarget_APIProperty();
		$val4key->Name				= "ItemNo";
		$val4key->Value			= $this->itemNo;

		$val5key						= new ExactTarget_APIProperty();
		$val5key->Name				= "FlagSize";
		$val5key->Value			= "5 X 8"; // value for DE field, note that characters may need to be escaped

		$val6key						= new ExactTarget_APIProperty();
		$val6key->Name				= "ShortDESC";
		$val6key->Value			= $this->shortDesc;

		$val7key						= new ExactTarget_APIProperty();
		$val7key->Name				= "UOMPrice";
		$val7key->Value			= $this->UOMprice; // notice this is a string and not a number or decimal

		$val8key						= new ExactTarget_APIProperty();
		$val8key->Name				= "Qty";
		$val8key->Value			= $this->Qty;

		$val9key						= new ExactTarget_APIProperty();
		$val9key->Name				= "subtotal";
		$val9key->Value			= $this->subtotal;

		$val10key					= new ExactTarget_APIProperty();
		$val10key->Name			= "newid"; // name of DE field
		$val10key->Value			= $this->newID;

		// add field values to the data extension
		$de->Properties[]			= $val1key;
		$de->Properties[]			= $val2key;
		$de->Properties[]			= $val3key;
		$de->Properties[]			= $val4key;
		$de->Properties[]			= $val5key;
		$de->Properties[]			= $val6key;
		$de->Properties[]			= $val7key;
		$de->Properties[]			= $val8key;
		$de->Properties[]			= $val9key;
		$de->Properties[]			= $val10key;

		$object						= new SoapVar($de, SOAP_ENC_OBJECT, 'DataExtensionObject', "http://exacttarget.com/wsdl/partnerAPI");

		// create the row of the data extension
		$request						= new ExactTarget_CreateRequest();
		$request->Options			= NULL;
		$request->Objects			= array($object);

		$result						= $client[$this->counter]->Create($request);

		/* output the results */
		/* this is for display and example purposes only, adjust the code to handle the success */

		echo "<pre>";
		var_dump($result);
		echo "</pre>";

	} catch (SoapFault $e) {
		var_dump($e);

		/* output the request and response */
		/* this is for display and example purposes only, adjust the code to handle the failure and fallback gracefully */

		print "Request: \n".
		$client[$this->counter]->__getLastRequestHeaders() ."\n";
		print "Request: \n".
		$client[$this->counter]->__getLastRequest() ."\n";
		print "Response: \n".
		$client[$this->counter]->__getLastResponseHeaders()."\n";
		print "Response: \n".
		$client[$this->counter]->__getLastResponse()."\n";
	
	}

}


public function order_confirmation_TS_ET() 
  {
	/* set the endpoint, notice that the account is on the s6 instance */
	$wsdl								= "https://webservice.s6.exacttarget.com/etframework.wsdl";

	try {
		/* Create the Soap Client */
		$client						= new ExactTargetSoapClient($wsdl, array('trace'=>1));


		/* Set username and password here */
		$client->username			= "################"; // add your API username here
		$client->password			= "################"; // add your API username here

	/* ExactTarget_TriggeredSendDefinition */
	$tsd							= new ExactTarget_TriggeredSendDefinition();
	$tsd->CustomerKey			= "ordercon"; // unique identifier for the triggered send definition
    // Necessary for our "active" TS
	$tsd->TriggeredSendStatus = ExactTarget_TriggeredSendStatusEnum::Active;


	/* ExactTarget_TriggeredSend */
	$ts							= new ExactTarget_TriggeredSend();
	$ts->TriggeredSendDefinition		= new SoapVar($tsd, SOAP_ENC_OBJECT, 'TriggeredSendDefinition', "http://exacttarget.com/wsdl/partnerAPI");


	/* Associate a subscriber and attributes to the triggered send */
	/* In this case no attributes were required */
	$ts->Subscribers			= array();
	$subscriber					= new ExactTarget_Subscriber();
	$subscriber->EmailAddress			= "###############"; // set the email address
	$subscriber->SubscriberKey			= "###############"; // use the email address for the SubscriberKey
	$ts->Subscribers[]		= $subscriber; // add the subscriber to the send


		/*% ExactTarget_Attribute */	
		$val2key = new ExactTarget_Attribute();
		$val2key->Name = "EmailAddress"; // name of DE field
		$val2key->Value = $this->email; // value for DE field
		/*% ExactTarget_Attribute */	
		$val3key = new ExactTarget_Attribute();
		$val3key->Name = "OrderID"; // name of DE field
		$val3key->Value = "32"; // value for DE field		
		/*% ExactTarget_Attribute */	
		$val4key = new ExactTarget_Attribute();
		$val4key->Name = "FnameS"; // name of DE field
		$val4key->Value = $this->firstNameS; // value for DE field
		/*% ExactTarget_Attribute */	
		$val5key = new ExactTarget_Attribute();
		$val5key->Name = "LnameS"; // name of DE field
		$val5key->Value = $this->lastNameS; // value for DE field
		/*% ExactTarget_Attribute */	
		$val6key = new ExactTarget_Attribute();
		$val6key->Name = "AddS1"; // name of DE field
		$val6key->Value = $this->address1S; // value for DE field
		/*% ExactTarget_Attribute */
		$val7key = new ExactTarget_Attribute();
		$val7key->Name = "AddS2"; // name of DE field
		$val7key->Value = $this->address2S; // value for DE field
		/*% ExactTarget_Attribute */	
		$val8key = new ExactTarget_Attribute();
		$val8key->Name = "CityS"; // name of DE field
		$val8key->Value = $this->cityS; // value for DE field
		/*% ExactTarget_Attribute */	
		$val9key = new ExactTarget_Attribute();
		$val9key->Name = "StateS"; // name of DE field
		$val9key->Value = $this->stateS; // value for DE field		
		/*% ExactTarget_Attribute */	
		$val10key = new ExactTarget_Attribute();
		$val10key->Name = "ZipS"; // name of DE field
		$val10key->Value = $this->zipS; // value for DE field
		/*% ExactTarget_Attribute */	
		$val11key = new ExactTarget_Attribute();
		$val11key->Name = "FnameB"; // name of DE field
		$val11key->Value = $this->firstNameB; // value for DE field
		/*% ExactTarget_Attribute */	
		$val12key = new ExactTarget_Attribute();
		$val12key->Name = "LnameB"; // name of DE field
		$val12key->Value = $this->lastNameB; // value for DE field
		/*% ExactTarget_Attribute */	
		$val13key = new ExactTarget_Attribute();
		$val13key->Name = "AddB1"; // name of DE field
		$val13key->Value = $this->address1B; // value for DE field
		/*% ExactTarget_Attribute */
		$val14key = new ExactTarget_Attribute();
		$val14key->Name = "AddB2"; // name of DE field
		$val14key->Value = $this->address2B; // value for DE field
		/*% ExactTarget_Attribute */	
		$val15key = new ExactTarget_Attribute();
		$val15key->Name = "CityB"; // name of DE field
		$val15key->Value = $this->cityB; // value for DE field
		/*% ExactTarget_Attribute */	
		$val16key = new ExactTarget_Attribute();
		$val16key->Name = "StateB"; // name of DE field
		$val16key->Value = $this->stateB; // value for DE field		
		/*% ExactTarget_Attribute */	
		$val17key = new ExactTarget_Attribute();
		$val17key->Name = "ZipB"; // name of DE field
		$val17key->Value = $this->zipB; // value for DE field		
		/*% ExactTarget_Attribute */	
		$val18key = new ExactTarget_Attribute();
		$val18key->Name = "OrderDate"; // name of DE field
		$val18key->Value = $this->date_add; // value for DE field
		/*% ExactTarget_Attribute */	
		$val19key = new ExactTarget_Attribute();
		$val19key->Name = "OrderTime"; // name of DE field
		$val19key->Value = $this->orderTime; // value for DE field
		/*% ExactTarget_Attribute */	
		$val20key = new ExactTarget_Attribute();
		$val20key->Name = "ordersubtotal"; // name of DE field
		$val20key->Value = $this->totalproduct; // value for DE field		
		/*% ExactTarget_Attribute */	
		$val21key = new ExactTarget_Attribute();
		$val21key->Name = "taxamount"; // name of DE field
		$val21key->Value = $this->tax; // value for DE field
		/*% ExactTarget_Attribute */	
		$val22key = new ExactTarget_Attribute();
		$val22key->Name = "shippingamount"; // name of DE field
		$val22key->Value = $this->shipping; // value for DE field
		/*% ExactTarget_Attribute */	
		$val23key = new ExactTarget_Attribute();
		$val23key->Name = "totalcost"; // name of DE field
		$val23key->Value = $this->total; // value for DE field		
		/*% ExactTarget_Attribute */	
		$val24key = new ExactTarget_Attribute();
		$val24key->Name = "cardtype"; // name of DE field
		$val24key->Value = $this->payment; // value for DE field
		/*% ExactTarget_Attribute */	
		$val25key = new ExactTarget_Attribute();
		$val25key->Name = "crypto_out"; // name of DE field
		$val25key->Value = "0000"; // value for DE field

		// add field values to the data extension

		$subscriber->Attributes[] = $val2key;
		$subscriber->Attributes[] = $val3key;
		$subscriber->Attributes[] = $val4key;
		$subscriber->Attributes[] = $val5key;
		$subscriber->Attributes[] = $val6key;
		$subscriber->Attributes[] = $val7key;
		$subscriber->Attributes[] = $val8key;
		$subscriber->Attributes[] = $val9key;
		$subscriber->Attributes[] = $val10key;
		$subscriber->Attributes[] = $val11key;
		$subscriber->Attributes[] = $val12key;
		$subscriber->Attributes[] = $val13key;
		$subscriber->Attributes[] = $val14key;
		$subscriber->Attributes[] = $val15key;
		$subscriber->Attributes[] = $val16key;
		$subscriber->Attributes[] = $val17key;
		$subscriber->Attributes[] = $val18key;
		$subscriber->Attributes[] = $val19key;
		$subscriber->Attributes[] = $val20key;
		$subscriber->Attributes[] = $val21key;
		$subscriber->Attributes[] = $val22key;
		$subscriber->Attributes[] = $val23key;
		$subscriber->Attributes[] = $val24key;
		$subscriber->Attributes[] = $val25key;


		// create SoapVar object
		$object						= new SoapVar($ts, SOAP_ENC_OBJECT, 'TriggeredSend', "http://exacttarget.com/wsdl/partnerAPI");

		// create request object
		$request						= new ExactTarget_CreateRequest();
		$request->Options			= NULL;
		$request->Objects			= array($object);

		// Create the triggered send definition
		$results						= $client->Create($request);


		/* output the results */
		/* this is for display and example purposes only, adjust the code to handle the success */
	
		echo "<pre>";
		var_dump($results);
		echo "</pre>";

	} catch (SoapFault $e) {
		var_dump($e);

		/* output the request and response */
		/* this is for display and example purposes only, adjust the code to handle the failure and fallback gracefully */

		print "Request: \n".
		$client->__getLastRequestHeaders() ."\n";
		print "Request: \n".
		$client->__getLastRequest() ."\n";
		print "Response: \n".
		$client->__getLastResponseHeaders()."\n";
		print "Response: \n".
		$client->__getLastResponse()."\n";

	}

}




}


?>