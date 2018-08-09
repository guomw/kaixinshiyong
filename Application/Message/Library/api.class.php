<?php 
namespace Message\Library;
class api 
{
	/* 发送站内信 [kza]
	 *
	 *	send_from_id 	：发送者ID
	 *	send_to_id 		：收信者ID
	 *	subject 		：标题
	 *	content 		：内容
	 *	
	 */
	public function send_mess($arr) {
		$data = array();
		$data['send_from_id'] = (int) $arr['send_from_id'];
		$data['send_to_id'] = (int) $arr['send_to_id'];
		$data['subject'] = $arr['subject'];
		$data['content'] = $arr['content'];
		$data['message_time'] = NOW_TIME;
		$result = model('message')->add($data);
		return $result;
	}
}

