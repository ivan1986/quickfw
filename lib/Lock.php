<?php
/**
 * Класс для реализации блокировок
 *
 * @author ivan
 */
class Lock
{
	/**
	 * Защита от двойного вызова cron скрипта
	 *
	 * @param string $name Идентификатор скрипта
	 * @param string $message Сообщение когда скрипт уже запущен
	 */
	static public function doubleRun($name, $message='')
	{
		static $lockfiles;
		if (!isset($lockfiles[$name]))
			$lockfiles[$name] = fopen(TMPPATH.'/'.$name.'.run', 'w');
		if (!$lockfiles[$name] ||
			!flock($lockfiles[$name], LOCK_EX | LOCK_NB)
			)
			die($message);
	}

	/** @var resourse Файл блокировки */
	private $file;
	/** @var stinrg Имя файла */
	private $name;

	/**
	 * Блокирует файл
	 *
	 * @param string $name Идентификатор блокировки
	 */
	public function  __construct($name)
	{
		$this->name = TMPPATH.'/'.$name.'.lock';
		$this->file = fopen($this->name, 'w');
		flock($this->file, LOCK_EX);
	}

	/**
	 * Разблокирует файл - удалять нельзя
	 */
	public function   __destruct()
	{
		flock($this->file, LOCK_UN);
		fclose($this->file);
	}

	/**
	 * Очистка всех файлов
	 */
	static public function clean()
	{
		$files = glob(TMPPATH.'/*.{run,lock}', GLOB_BRACE);
		foreach($files as $file)
			unlink($file);
	}

}
?>
