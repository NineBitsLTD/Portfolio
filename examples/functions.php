<?php
/**
 * Получить из словаря переводы определенного языка
 * 
 * @param string $lng
 * @param array $arr
 * @return array
 */
function getByLng($lng, $arr){
    return array_map(
        function($item) use ($lng){
            if(!is_array($item)) return $item;
            else if(array_key_exists($lng, $item)) return $item[$lng];
            else return '';
        },
        $arr
    );
}
// Пример выполнения getByLng
print_r(getByLng(
    'rus',
    [
        'start'=>['rus'=>'Старт', 'eng'=>'Start'],
        'stop'=>['rus'=>'Стоп', 'eng'=>'Stop'],
    ]
));


/**
 * Использование динамического вызова функций
 * 
 * Произвольная функция
 */
function test() {
    echo "Test ".join(',', func_get_args())." \n";
}
/**
 * Произвольная лямбда функция
 */
$func = function($arg1, $arg2) {
    return $arg1 * $arg2;
};
/**
 * Произвольный класс со статической функцией
 */
class ClassA
{
    const NAME = 'A';
    public static function test() {
        echo static::NAME, " ".join(',', func_get_args())." \n";
    }
    public function method(){
        echo static::NAME, " method\n";
    }
}
/**
 * Клас динамически вызывающий произвольную функцию и статическую функцию класса ClassA
 */
class ClassB
{
    const NAME = 'B';

    public static function test() {
        echo self::NAME, "\n";
        forward_static_call_array(array('ClassA', 'test'), array('more', 'args'));
        forward_static_call_array( 'test', array('other', 'args'));
    }
}

print_r(ClassB::test('foo'));

print_r(call_user_func_array('test', array(1,2)));
print_r(call_user_func_array(array(new ClassA(),'method'), array(3,4)));
print_r(call_user_func_array($func, array(5,6)));


