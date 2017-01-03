<?php
/**
 * Instructions:
 * Write a solution in PHP:
 *
 * How your implementation works:
 * Your function will take two arguments, ($prevArray, $currArray), flattens the objects inside of prevArray and currArray to 1 level of
 * depth, and return an HTML Table in string form of the values.  The HTML table you return has a column header which is a superset of all keys in 
 * all the objects in the currArray.  Any values that have changed from the prevArray to the currArray (ie field value changed or is a 
 * new key altogether) should be bolded. In the case that the value has been removed altogether from the prevArray to the currArravy, 
 * you will write out the key in bold DELETED.
 * 
 * Rules:
 * 1. The arrays are arbitrarily deep (see common questions for explanation of arbitrarily deep).
 * 2. The currArray could have more or potentially even be in a different index order.  You cannot depend solely on array index for  
 * comparison.  However, you can assume that each object in the arrays will have an "_id" parameter.  Unless the currArray has no  
 * object with the matching "_id" parameter (for example if the whole row has changed).
 * 3. Do not create global scope.  We have a test runner that will iterate on your function and run many fixtures through it.  If you 
 * create global scope for 1 individual diff between prevArray to currArray you could cause other tests to fail.  
 *
 * Common Questions:
 * 1. Can I use outside packages to solve (e.g. Composer)?  Yes.  You can use any packages you want to solve the solution.  
 * 2. Can I use google or outside resources (e.g. StackOverflow, GitHub)?  Yes.  Act as you would in your day job.
 * 3. What does arbitrarily deep mean? The $prevArray or $currArray can have objects inside of objects at different levels of depth. 
 *    You will not know how many levels of depth the objects could have, meaning your code must handle any kind of object.  Your 
 *    solution  must account for this.  Do not assume the examples below are the only fixtures we will use to test your code. 
 * 
 * @param $prevArray is a JSON string containing an array of objects
 * @param $currArray is a JSON string containing an array of objects
 * @return a string with HTML markup in it, should return null if error occurs.
 */

// $prevArray = '[{"_id":1,"someKey":"RINGING","meta":{"subKey1":1234,"subKey2":52}}]';
// $currArray = '[{"_id":1,"someKey":"HANGUP","meta":{"subKey1":1234}},{"_id":2,"someKey":"RINGING","meta":{"subKey1":5678,"subKey2":207,"subKey3":52}}]';

// function arrayDiffToHtmlTable( $prevArray, $currArray) {
//     //IMPLEMENT

//     return $htmlTableString;
// }

// Example, Given the following data set:
//
//        echo arrayDiffToHtmlTable( $prevArray, $currArray);
//
//  OUTPUT (Note this is a text representation... output should be an HTML table):
//
//          _id               someKey          meta_subKey1        meta_subKey2        meta_subKey3
//            1              **HANGUP**             1234              **DELETED**
//          **2**            **RINGING**          **5678**             **207**             **52**
//
//  ** implies this field should be bold or highlighted.
//  !!! analyze the example carefully as it demonstrates expected cases that need to be handled. !!!
//


$prevArray = '[{"_id":1,"someKey":"RINGING","meta":{"subKey1":1234,"subKey2":52}}]';
$currArray = '[{"_id":1,"someKey":"HANGUP","meta":{"subKey1":1234}},{"_id":2,"someKey":"RINGING","meta":{"subKey1":5678,"subKey2":207,"subKey3":52}}]';

echo arrayDiffToHtmlTable($prevArray,$currArray);

/**
*@$arrayName=Multidimensional array
**/

function check_array_multidimensional($arrayName)
{
	if (count($arrayName) == count($arrayName, COUNT_RECURSIVE)) 
	{
		return false;
	}
	else
	{
		return true;
	}
}

/**
**@$array1=Previous array
**@$array2=current array
**/

function array_diff_assoc_recursive($array1, $array2)
{
	foreach($array1 as $key => $value)
	{
		if(is_array($value))
		{
			if(!isset($array2[$key]))
			{
				$difference[$key] = $value;
			}
			elseif(!is_array($array2[$key]))
			{
				$difference[$key] = $value;
			}
			else
			{
				$new_diff = array_diff_assoc_recursive($value, $array2[$key]);
				if($new_diff != FALSE)
				{
					$difference[$key] = $new_diff;
				}
			}
		}
		elseif(!isset($array2[$key]) || $array2[$key] != $value)
		{
			if(isset($array2[$key]))
			{

			$difference[$key] = $array2[$key];
			}
			else{
			$difference[$key] = "<b>DELETED</b>";

			}
		}
		else{
			$difference[$key] = $array2[$key];

		}
	}
	return !isset($difference) ? 0 : $difference;
}

function mk_key($final_keys,$fvalue,$thekey)
{
	foreach ($fvalue as $f2key => $f2value) {
			if (is_array($f2value)) {
				$thekey=$thekey."_".$f2key;
				if(!in_array($thekey, $final_keys))
				{
				mk_key($final_keys,$f2value,$thekey);
				}
			}
			else
			{
				$thekey2=$thekey."_".$f2key;
				if(!in_array($thekey2, $final_keys))
				{
				array_push($final_keys, $thekey2);
				}
			}
		}	

return $final_keys;
}

function mk_value($final_keys,$fvalue,$thekey,$final_values)
{
	foreach ($fvalue as $f2key => $f2value) {
			if (is_array($f2value)) {
				$thekey=$thekey."_".$f2key;
				mk_value($final_keys,$f2value,$thekey,$final_values);
			}
			else
			{
				$thekey2=$thekey."_".$f2key;

				array_push($final_keys, $thekey2);
					$final_values[$thekey2]=$f2value;

			}
		}	

return $final_values;
}



function arrayDiffToHtmlTable($prevArray,$currArray)
{
	$prev=json_decode($prevArray, true);
	$curr=json_decode($currArray, true);

	$difference=array_diff_assoc_recursive($prev, $curr);
	$current_array=array_replace($curr, $difference);
	$final_keys=array();
	$final_values=array();
	$final_main_value=array();
	foreach ($current_array as $fmainkey => $fmainvalue) {
		foreach ($fmainvalue as $fkey => $fvalue) {
			
			if(is_array($fvalue))
			{
				$final_keys=mk_key($final_keys,$fvalue,$fkey);
			}
			else
			{
				if(!in_array($fkey, $final_keys))
				{
					array_push($final_keys, $fkey);

				}

			}

		}
	
	}
	foreach ($current_array as $fmainkey => $fmainvalue) {
		foreach ($fmainvalue as $fkey => $fvalue) {
			
			if(is_array($fvalue))
			{
				$final_values=mk_value($final_keys,$fvalue,$fkey,$final_values);
			}
			else
			{

					$final_values[$fkey]=$fvalue;

			}

		}
			array_push($final_main_value, $final_values);
			$final_values=array();
	}


	$data= "<table border='1'><thead><tr>";
	foreach ($final_keys as $fmvalue) {
		$data.="<th>".$fmvalue."</th>";
	}
	$data.="</tr></thead><tbody>";
	foreach ($final_main_value as $fkey2=>$fmvalue2) {
		$data.="<tr>";

		foreach ($final_keys as $fmvalue29) {

			if (isset($fmvalue2[$fmvalue29])) {
				# code...
			$data.="<td>".$fmvalue2[$fmvalue29]."</td>";
			}
			else{
			$data.="<td></td>";
				
			}
			}
		$data.="</tr>";
	}
	$data.="</tbody></table>";

return $data;
}
?>