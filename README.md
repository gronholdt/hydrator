Hydrator
========

[![Build Status](https://travis-ci.org/solbianca/hydrator.svg?branch=master)](https://travis-ci.org/solbianca/hydrator)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/solbianca/hydrator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/solbianca/hydrator/?branch=master)

Hydrator can be used for two purposes:

- To extract data from a class.
- To fill an object with data or create a new instance of a class filled with data.

In both cases it is saving and filling protected and private properties without calling
any methods which leads to ability to persist state of an object with properly encapsulated
data. Any static properties will be ignored.


## Installation

The preferred way to install this package is through [composer](http://getcomposer.org/download/).

```
composer require --prefer-dist solbianca/hydrator
```

## Usage

Consider we have a `Post` entity which represents a blog post. It has a title and a text. A unique id is generated to
identify it.

```php
class Post
{
    private $id;
    protected $title;
    protected $text;

    public function __construct($title, $text)
    {
        $this->id = uniqid('post_', true);
        $this->title = $title;
        $this->text = $text;
    }
   
    public function getId()
    {
        return $this->id;
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    public function getText()
    {
        return $this->text;
    }
    
    public function setText()
    {
        return $this->text;
    }
}
```

Saving a post to database:

```php
$post = new Post('First post', 'Hell, it is a first post.');

$hydrator = new \SolBianca\Hydrator\Hydrator();

$data = $hydrator->extract($post);
save_to_database($data);

  OR

$data = $hydrator->extract($post, ['id', 'title']); // extract id and title form object
save_to_database($data);
```

Loading post from database:

```php
$data = load_from_database();

$hydrator = new \SolBianca\Hydrator\Hydrator();

$post = $hydrator->hydrate(Post::class, $data);
echo $post->getId();
```

Filling existing post object with data:

```php
$data = load_from_database();

$hydrator = new \SolBianca\Hydrator\Hydrator();

$post = get_post();
$post = $hydrator->hydrate($post, $data);
echo $post->getTitle();
```
