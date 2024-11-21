# AutoType

Library for auto-registering value objects (crates, DTOs) as Doctrine types.

Wouldn't it be nice if you could use your Value Objects as entity type directly?
With AutoType you do not have to create [doctrine type](https://www.doctrine-project.org/projects/doctrine-orm/en/3.3/cookbook/custom-mapping-types.html)
for every single value object type.

## Usage

```shell
composer require martingold/autotype
```
You have few options:
1. Add `#[ValueGetter]` and `#[Constructor]` (not required) attributes to your ValueObject, DTO, Crate, ...
2. Make your objects implement ValueObject interface
3. Create your own driver (implement your own `TypeDefinitionDriver`). See below 👇

Example using attributes:
```php
final readonly class Url
{
    private function __construct(
        private string $value,
    ) {
    }
    
    // Regular constructor is used when attribute not found
    #[Constructor]
    public static function create(string $url): self
    {
        if (filter_var($value, FILTER_VALIDATE_URL) === false) {
            throw MalformedUrl::fromString($value);
        }
        
        return new self($url)
    }
    
    #[ValueGetter]
    public function getValue(): string
    {
        return $this->value;
    }
    
    public function isSecure(): bool
    {
        return str_starts_with('https://', $this->value);
    }
}
```

Register types at the entry point of your application (kernel boot when using symfony):
```php
// Get a PSR-6 cache instance
$cache = $this->container->get(CacheItemPoolInterface::class);

// Alternatively, use Doctrine's PSR-6 metadata cache
$entityManager = $this->container->get(EntityManagerInterface::class);
$cache = $entityManager->getConfiguration()->getMetadataCache();

// Create a type provider
$cachedTypeFinder = new CachedTypeDefinitionProvider(
    new DefaultTypeDefinitionProvider(__DIR__ . '/../ValueObject'),
    $cache
);

// Register dynamic types
(new DynamicTypeRegistry($cachedTypeFinder))->register();
```

Use the value object directly in your entities.
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

### Drivers
See `tests/Entity` for the example usage of the drivers. The library comes with two default drivers:
#### AttributeTypeDefinitionDriver
This driver registers all classes with a `#[ValueGetter]` method as Doctrine types. If a static factory
method is needed, add the `#[Constructor]` to the method which should be used for constructing the object back from
database value.
#### InterfaceTypeDefinitionDriver
This driver registers all classes implementing `ValueObject` interface.

#### Custom driver
If you have existing value objects based on your project's conventions
and do not want to add additional interfaces or custom attributes, you can implement your own driver and use it during type registration:
```php
$typeDefinitionProvider = new CachedTypeDefinitionProvider(
    new ScanTypeDefinitionProvider($sourceFolder, [
        new CustomTypeDefinitionDriver(),
    ]),
    $cache,
);

(new DynamicTypeRegistry($typeDefinitionProvider))->register();
```

The possibilities are endless. You can even specify your own custom dynamic types in
case you have special requirements like column length or database-specific optimizations.
See `AttributeTypeDefinitionDriver` and `InterfaceTypeDefinitionDriver` for more examples.

```php
class CustomTypeDefinitionDriver implements TypeDefinitionDriver
{
    /**
     * Should be the class treated as doctrine type?
     * @param ReflectionClass<object> $class
     */
    public function supports(ReflectionClass $class): bool
    {
        return str_ends_with($class->getShortName(), 'Crate');
    }

    /**
     * Get dynamic type class. Whether it is value a string or int.
     * @param ReflectionClass<object> $class
     *
     * @return class-string<DynamicType&Type>
     */
    public function getDynamicTypeClass(ReflectionClass $class): string
    {
        return match ($this->getValueMethodReturnType($class)) {
            'string' => StringDynamicType::class,
            'int' => IntegerDynamicType::class,
            default => throw new UnsupportedType('Only string|int type is supported.'),
        };
    }

    /**
     * Name of the method which should be used when persisting object to database. 
     * @param ReflectionClass<object> $class
     */
    public function getValueMethodName(ReflectionClass $class): string
    {
        return 'getValue';
    }

    /**
     * Method to use when creating the object from database value. Must have single argument. 
     * When null returned, the regular constructor is used.  
     */
    public function getConstructorMethodName(ReflectionClass $class): string|null
    {
        return $class->hasMethod('of') ? 'of' : null;
    }

    /**
     * Get value getter method return type to determine if database value should be string or int
     * @param ReflectionClass<object> $class
     *
     * @throws UnsupportedType
     */
    private function getValueMethodReturnType(ReflectionClass $class): string
    {
        $returnType = $class->getMethod('getValue')->getReturnType();

        if (!$returnType instanceof ReflectionNamedType) {
            throw new UnsupportedType("Intersection or union return type not supported in method {$class->getName()}::getValue()");
        }

        if (!$returnType->isBuiltin()) {
            throw new UnsupportedType("Only scalar return types are supported in {$class->getName()}::getValue()");
        }

        return $returnType->getName();
    }
}

```