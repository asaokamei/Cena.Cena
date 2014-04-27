Cena.Cena
=========

Cena is "Composite Entity Notation and Augmentation", 
a technology about text representation of entity objects state.

### license

t.b.d.

### patent

Patented in Japan (#4782895), and patent pending in the US. 
so, please be careful when using this software... 

About Cena
----------

For each entity object, Cena represent it the following in text:

*   lifecycle (how it is generated),
*   its properties, and
*   relation with other entities. 

Cena will simplifies various operations such as 
database synchronization and complicated html forms. 

Entity State and Text Representation
------------------------------------

Given that the class name of entities are "Model". 

An entity is represented as a simple text, called CenaID. 
For instance: ```Cena.model.new.1```, ```Cena.model.get.1```. 


### Entity LifeCycles

Create a new entity of Model.

```
// Cena.model.new.1
$entity = new Model();
```

Retrieve an entity from a database. 

```
$entity = Model::findById(1);
// Cena.model.get.1
```

### Entity's Properties

To represent property values of an entity, just specify 
property name following the CenaID.

```
Cena.model.new.1.prop.name = 'my name'
Cena.model.get.1.prop.name = 'your name'
```


### Relation Between Entities.

Cena uses CenaID to represent relations between entities. 

```
Cena.model.new.1.link.related = Cena.other.get.1
```

The above simple notation is almost equivalent with 
the following PHP code.

``` 
$entity = new Model();
$entity->setRelated( Other::findById(1) );
```

Protocol (tentative)
--------------------

current protocol looks like: 

```json
{
  cenaID: {
    prop: { field1: value1, field2: value2, ... },
    link: { rel1: cenaID2, rel2: [ cenaID3, cenaID4,...],... }
    error: { field5: message5, field6: message6,... }
    info: {
      orig-cenaID: OriginalCenaId,...
    }
  }
}
```


### Cena is Unique At...

So, what are the differences between Cena and other similar 
technologies? Honestly, I (the author) is not familiar with 
other technologies yet, so I cannot be certain but...

The following are (probably) the unique features of Cena.

*   easy to describe new entities (Cena.model.new.1). 
*   easy to manipulate many entities, new or existent. 
*   easy to relate new entities (i.e. no primary key) with 
    other entities.

CenaManager
===========

Currently, Doctrine2 can be used as a base ORM for Cena. 

