TagBundle For Symfony2
=========

##About

The goal of this bundle is to allow you to associate tags with several entities.


##Installation

1. Add package to `require` in `composer.json`:

	```json
	"require": {
	    "mykees/symfony2-tag-bundle": "1.0.*@dev"
	}
	```
or
	```
	$ php composer.phar require mykees/symfony2-tag-bundle "1.0.*@dev"
	```

2. Add this bundle to your application's kernel :

	```php
	$bundles = array(
            new Mykees\TagBundle\MykeesTagBundle(),
        );
	```

3. Add routing in your `app/config/routing.yml`:

	```yml
	mykees_tag_admin:
		resource: "@MykeesTagBundle/Resources/config/routing_admin.yml"
	    prefix:   /admin/tag
	```

4. Update your database:

	```
	php app/console doctrine:schema:update --force
	```


##Setup

1. Add `Taggable` interface and `TaggableTrait` trait, in your entity as in the following example :

	```php
	namespace Mykees\BlogBundle\Entity;

	use Doctrine\ORM\Mapping as ORM;
	use Mykees\TagBundle\Interfaces\Taggable;
	use Mykees\TagBundle\Traits\TaggableTrait;

	/**
	 *Post
	 *@ORM\Table()
	 *@ORM\Entity(repositoryClass="Mykees\BlogBundle\Entity\PostRepository")
	 *@ORM\HasLifecycleCallbacks()
	 */
	class Post implements Taggable
	{
	    use TaggableTrait;

		.....

	}
	```
Now you can add or retrieve tags associated with your entity.


##Add Tags

1. In your form type, add `tags` field (*You can add text field options if you need*) :

	```php
	$builder
        ->add('name','text',[
            'label'=>'Titre : '
        ])

        ...

        ->add('tags','mk_tag',[
            'label'=>'Les Tags:',
        ])
    ;
	```
	
2. Add input text in your template.

	```php
	{{ form_row(form.tags) }}
	```

Now you can add tags **separated by a comma** as in the following example :

```twig
tag1,tag2,tag3,tag with spaces,add tag with mykees tag bundle,......
```


##Retrieve Tags

In your controller actions, to get all tags associated with your entity, you can use **findTagRelation()** function of the 'mk.tag_manager' service:

```php
public function indexAction()
{
	$posts = $this->getManager()->getRepository('MykeesBlogBundle:Post')->findAll();
	$this->get('mk.tag_manager')->findTagRelation($posts);
}
```

And in your template you can display the tags like that:

```twig
{% if post.getTags() %}
	{% for tag in post.getTags %}
		<span class="label label-primary"> {{ tag.name }} </span>
	{% endfor %}
{% endif %}
```


##Remove Tags

For more comfort, removing tags uses **ajax**.

1. In your edit action in your controller, get all tags associated with your entity like that:

	```php

	public function editAction( $id=null, Request $request )
    {
        if( $id ){
            $post = $this->getManager()->getRepository('MykeesBlogBundle:Post')->find($id);
        }else{
            $post = new Post();
        }
        
        $this->get('mk.tag_manager')->findTagRelation($post);

    	......

	}
    ```

2. Now in your template, add the following block outside your block content:

	```twig
	{% block javascripts %}
	    {{ parent() }}
	    {{ javascript() }}
	{% endblock %}
	```

Make sure that your main layout contain `block javascripts` with **jquery** like that:

```twig
{% block javascripts %}
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
{% endblock %}
```

3. Now you can delete the tags by adding a link to the delete action like the following example:

	```twig
	{% for tag in post.getTags %}
       {% if tag %}
           <span class="label label-primary">
           {{ tag.name }}

           {% for tr in tag.getTagRelation %}
               <a href="{{ path('mykees_tag_delete_relation',{'relation_id':tr.id}) }}" class="delTag">[x]
               </a>
           {% endfor %}

           </span>
       {% endif %}
    {% endfor %}
	```
Don't forget to add attribute class **deltTag** on your link.




##Useful functions

1. Save relation
	
	```php
	$this->get('mk.tag_manager')->saveRelation(Taggable $model)
	```

2. Delete relation
	
	```php
	$this->get('mk.tag_manager')->deleteTagRelation(Taggable $model)
	```

3. find tags associated with an entity

	```php
	$this->get('mk.tag_manager')->findTagRelation($model)
	```

4. find tags by name

	```php
	$this->get('mk.tag_manager')->findByName($names)
	```

5. find entity ids associated to tags

	```php
	$this->get('mk.tag_manager')->findReferer( $slug_tag, $model_type=null )
	```



##Testing

If you want to add or modify some functionnality, you can test them in `Tests/Controller/TagsControllerTest.php`.

Enjoy ;)

