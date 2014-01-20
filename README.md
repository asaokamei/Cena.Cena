Cena.Cena
=========

Composite Entity Notation and Augmentation for PHP, is,

*   A DSL/protocol for manipulating ORM entities...

*   for transporting entities over http, or even html forms,


CenaManager
-----------

```php
// set up CenaManager
$cm = CenaManager::factory();
$cm->setClass( 'MyModels\Task' ); // use MyModels\Task class as Task

// get some Task entities.
$task1 = $cm->getEntity( 'Task', 1 );
$task2 = $cm->newEntity( 'Task' );
$cm->assign( $task2, array( 'task' => 'new task' ) );
$cm->relate( $task1, 'parent', $task2 );
$cm->save();
```


A SubSet is a collection
------------------------

experimental and imaginary coding.

```php
// generate Task entities.
$task1 = $cm->getEntity( 'Task', 1 );
$task2 = $cm->newEntity( 'Task' ); // status is "active"...

// create a new subset.
$sub1  = $cm->subset( 'Task', array( 'status' => task::STATUS_ACTIVE ) );
$sub1->register( $task2 );

// set relation to all entities in the $sub1.
$cm->relate( $sub1, 'parent', $task1 );

```

「Cena集合」というのを考えた場合...

Cena集合のエンティティが何かを判断するのは最後に
行う（Late Binding）のが望ましいとかんがえる。

もし、relateが即時実行でDoctrine2などの実体に
変換されるとなると、Late Bindingにならない。

つまりリレーションはCena集合のオブジェクトの形で
持っている必要がある。

無理！
遅延評価は諦めるか。

すごそうだけど、今のところ実装大変そうだし、
実用性も未知数なので無視します。


Wishes (Laravel Style)
----------------------

###data manipulation.


```php
Cena::Task->fetch(1)->set( 'task' => 'got task' );
Cena::Task->forge()->assign( array( 'task' => 'new task' ) );
Cena::save();
```

relation.

```php
Cena::Task->forge(1)->relate( 'parent' => Cena::task->fetch(1) );
Cena::save();
```


