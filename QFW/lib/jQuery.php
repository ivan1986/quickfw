<?php
/**
 * Бэкенд для удобного использования JQ
 *
 * @author ivan
 */
class jQuery
{
	/**
	 * Устанавливает заголовок и кодирует данные
	 *
	 * @param mixed $data данные для возврата браузеру
	 * @return string json
	 */
	public function json($data)
	{
		header('Content-type: application/json');
		return json_encode($data);
	}
}

