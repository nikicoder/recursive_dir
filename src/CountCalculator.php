<?php

namespace DirListener;

class CountCalculator
{ 
    /**
     * Сумма прочитанных чисел 
     *
     * @var int
     */
    protected $count = 0;
        
    /**
     * Количество прочитанных файлов
     *
     * @var int
     */
    protected $countFiles = 0;
    
    /**
     * Корневая директория в которой выполняется поиск файлов
     *
     * @var string
     */
    protected $rootFolder = '';
    
    /**
     * __construct
     *
     * @param  mixed $path
     * @return void
     * @throws \Exception
     */
    public function __construct(string $path)
    {
        if (!is_dir($path)) {
            throw new \Exception('Указанный путь ' . $path . ' не является директорией');
        }

        $this->rootFolder = $path;
    }
        
    /**
     * Запуск подсчета
     *
     * @return $this
     */
    public function run()
    {
        $this->parseDirectory();

        return $this;
    }
    
    /**
     * Возвращает результат или выбрасывает исключение 
     * если 0 файлов было обработано.
     *
     * @return int
     * @throws \Exception
     */
    public function getResult(): int
    {
        if ($this->countFiles <= 0) {
            $msg = 'По указаному пути ' . $this->rootFolder . ' нет ни одного файла с именем count';
            throw new \Exception($msg);
        }

        return $this->count;
    }
    
    /**
     * parseDirectory
     *
     * @return void
     */
    private function parseDirectory()
    {
        // В ТЗ не было заявлено поддержки PHP4
        // по этому считаю более правильным чем писать свою реализацию
        // рекурсивного обхода дерева воспользоваться RecursiveDirectoryIterator
        // https://www.php.net/manual/ru/class.recursivedirectoryiterator.php
        // доступного с PHP5 в стандартной поставке
        // как минимум это быстрее работать будет и код короче
        
        $dir = new \RecursiveDirectoryIterator($this->rootFolder);
        $iterator = new \RecursiveIteratorIterator($dir);
        
        // В зависимости от ОС слеш может быть разным
        // слеш нужен чтобы отфильтровать только файлы с именем count
        // что будет если кому нибудь придет в голову создать файл uncount или _count
        $regex = DIRECTORY_SEPARATOR === '/' ?
            '/^.+\/count$/i' : '/^.+\\count$/i';
        $result = new \RegexIterator($iterator, $regex, \RecursiveRegexIterator::GET_MATCH);
        
        // foreach безопасен, лишнее отфильтровано регексом выше
        foreach ($result as $item) {
            $this->parseCountResultAndUpdateTotals($item);
        }

        return;
    }
    
    /**
     * parseCountResultAndUpdateTotals
     *
     * @param  mixed $item
     * @return void
     */
    private function parseCountResultAndUpdateTotals($item)
    {
        $path = '';

        // заложим сразу исходя из того что item mixed 
        // возможность в будушем переиспользовать этот код для 
        // переменных других типов
        if (is_array($item)) {
            $path = $item[0];
        }

        if (empty($path) || !is_file($path)) {
            return;
        }

        $data = file_get_contents($path);

        if (empty($data)) {
            return;
        }

        // сделаем допущение что кроме int-а там в нормальной ситуации ничего лежать не должно
        // в противном случае преобразование типа нужно вынести в парсер данных полученной строки
        settype($data, 'integer');

        $this->count += $data;
        $this->countFiles++;

        return;
    }
}