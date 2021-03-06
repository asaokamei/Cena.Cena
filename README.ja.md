Cena.Cena
=========

Cenaは「Composite Entity Notation and Augmentation」の略で、
エンティティオブジェクトの状態をテキスト表記する技術のことです。

### ライセンス

t.b.d.

### パテント

日本では特許取得済み（特許第４７８２８９５号）、米国ではパテント取得中。
使用する場合は注意されたし。

Cenaについて
------------

エンティティの状態とは、各エンティティについて、

*   ライフサイクル（生成）、
*   プロパティ、
*   他のエンティティとのリレーション、

の状態を簡潔にテキスト表記できます。

これにより、データベースの同期や複雑なフォームの構成など、
比較的簡単に行うことができます。

エンティティの状態とテキスト表記
----------------------------

エンティティのクラスを「model」として説明します。

一つのエンティティを、簡潔なテキストで表現します。
これをCenaIDと呼んでいます。

例：```Cena.model.new.1```、```Cena.model.get.1```


### エンティティとライフサイクル表現

新しいmodelのエンティティを作成する。

```
// Cena.model.new.1
$entity = new Model();
```

既存のエンティティをデータベースから読み出す。

```
$entity = Model::findById(1);
// Cena.model.get.1
```

### エンティティのプロパティ

エンティティのプロパティを表す場合は、CenaIDに続けて
プロパティを指定します。

```
Cena.model.new.1.prop.name = 'my name'
Cena.model.get.1.prop.name = 'your name'
```


### エンティティ間のリレーション

エンティティ間のリレーションはCenaIDを使って表現します。

```
Cena.model.new.1.link.related = Cena.other.get.1
```

これで、下記のPHPを走らせた場合とほぼ同等になります。

``` 
$entity = new Model();
$entity->setRelated( Other::findById(1) );
```

### Cenaの特徴

Cenaを使った場合、他の似たような技術と何が違うのでしょう。
正直に言えば、他の技術について勉強不足なので正確に判断できる
段階ではありませんが、おそらく次の点になると思います。

*   新規エンティティを簡潔に表現できる（Cena.model.new.1)。
*   既存も新規のエンティティも複数を一度の表現できる。
*   エンティティ間のリレーションを、エンティティを使って表現できる。
    このため、新規エンティティでもリレーションを表現できる。

CenaManager
===========

現状では、Doctrine2をCenaのベースORMとして使うことができます。

