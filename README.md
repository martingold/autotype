# AutoType

Library for auto-registering value objects (crates, DTOs) as Doctrine types.

Would not be nice if you could use your Value Objects as entity type directly?
You have to create [doctrine type](https://www.doctrine-project.org/projects/doctrine-orm/en/3.3/cookbook/custom-mapping-types.html)
for every single value object type. This is where AutoType comes in.

```php
final readonly class Url
{
    private string $value;

    /**
     * @throws MalformedUrl
     */
    public function __construct(
        string $value,
    ) {
        if (filter_var($value, FILTER_VALIDATE_URL) === false) {
            throw MalformedUrl::fromString($value);
        }

        $this->value = $value;
    }
    
    public function isSecure(): bool
    {
        return str_starts_with('https://', $this->value);
    }

    #[ValueGetter]
    public function getValue(): string
    {
        return $this->value;
    }
}
```

```php
#[Entity]
class Company
{
    #[Column]
    private string $name;

    #[Column(type: Url::class)]
    private Url $url;
}
```
## Usage

```shell
composer require martingold/autotype
```

### Registering Value Objects
All classes within source folder containing one method marked with `#[ValueGetter]`
are registered. You may use a `#[Constructor]` attribute to use value object factory method.

### Bootstraping

You need to register types at the entry point of your application:

```php
// Get PSR-6 cache somewhere
$cache = $this->container->get(CacheItemPoolInterface::class);

$cachedTypeFinder = new CachedDynamicTypeProvider(
    new DefaultDynamicTypeFinder(__DIR__ . '/../ValueObject'),
    $cache
);

(new DynamicTypeRegistry($cachedTypeFinder))->register();
```
Finding the types is quite heavy operation and
the dynamic type definitions should be cached (or better saved in a compiled DI container for later usage).

### Symfony 
If you are using Symfony, you may use `martingold/autotype-symfony` (soon™).
It finds types using during container compilation and is cached in container itself.

## Concepts

### DynamicType
Represents doctrine type. It knows how to convert value object to database value and back.
There should be one child of `DynamicType` per possible value object return type (string, int, ...).

### TypeDefinitionFactory
Class responsible for determining which classes should be treated as auto-registered dynamic types and
which methods should be used to convert object to database value and to construct the object back.

## Advanced usage
### Custom object instantiation
The `#[Constructor]` attribute can be used to used to tell the library to use custom method for constructing the object.

### Existing value objects
When you have existing way of dealing with value objects (hydrating them from request, ... ) and you do not want to
clutter all value objects with attributes, you can implement your own TypeDefinitionFactory. The TypeDefinitionFactory is cached,
so you should not worry about performance impact.

Example for value objects using ValueObject interface:
```php
/**
 * @template T of scalar|null
 */
interface ValueObject
{
    /**
     * @return T
     */
    public function getValue(): mixed;
}
```

```php
// Get PSR-6 cache somewhere
$cache = $this->container->get(CacheItemPoolInterface::class);

$cachedTypeFinder = new CachedDynamicTypeProvider(
    new ComputeTypeDefinitionFinder(__DIR__ . '/../ValueObject', [
       new InterfaceValueObjectTypeDefinitionFactory()
    ]),
    $cache
);

(new DynamicTypeRegistry($cachedTypeFinder))->register();
```

```php
class InterfaceValueObjectTypeDefinitionFactory implements TypeDefinitionFactory
{
    /**
     * @param ReflectionClass<object> $class
     */
    public function supports(ReflectionClass $class): bool
    {
        return $class->isSubclassOf(ValueObject::class);
    }

    /**
     * @param ReflectionClass<object> $class
     *
     * @return class-string<DynamicType&Type>
     */
    public function getDynamicTypeClass(ReflectionClass $class): string
    {
        return match ($this->getValueMethodReturnType($class)) {
            'string' => StringDynamicType::class,
            default => throw new UnsupportedType('Only string type is supported.')
        };
    }

    /**
     * @param ReflectionClass<object> $class
     */
    public function getValueMethodName(ReflectionClass $class): string
    {
        return 'getValue';
    }

    public function getConstructorMethodName(ReflectionClass $class): string|null
    {
        // null means native constructor will be used
        return null;
    }

    /**
     * @param ReflectionClass<object> $class
     */
    private function getValueMethodReturnType(ReflectionClass $class): string
    {
        $returnType = $class->getMethod('getValue')->getReturnType();

        if (!$returnType instanceof ReflectionNamedType) {
            throw new UnsupportedType("Intersection or union return type not supported in method {$class->getName()}::{$method->getName()}()");
        }

        if (!$returnType->isBuiltin()) {
            throw new UnsupportedType("Only scalar return types are supported in {$class->getName()}::{$method->getName()}()");
        }

        return $returnType->getName();
    }
}
```