<?php
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 10.12.16
 * Time: 10:19
 */
namespace Plumbok;

use Doctrine\Common\Annotations\AnnotationRegistry;
use phpDocumentor\Reflection\Types\Context as TypeContext;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use Plumbok\Annotation\Getter;
use Plumbok\Annotation\Setter;
use Plumbok\Compiler\Context;
use Plumbok\Compiler\Generator\Getter as GetterGenerator;
use Plumbok\Compiler\Generator\Setter as SetterGenerator;
use Doctrine\Common\Annotations\DocParser;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Serializer;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Mixed;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Parser;
use Plumbok\Compiler\Statements;
use Plumbok\Compiler\Tags;

/**
 * Class GeneratorParser
 * @package Plumbok
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
class Compiler
{
    /** @var Parser */
    private $phpParser;
    /** @var DocParser */
    private $docParser;
    /** @var Serializer */
    private $docBlockSerializer;

    /**
     * Compiler constructor.
     */
    public function __construct()
    {
        $this->phpParser = (new ParserFactory)->create(ParserFactory::ONLY_PHP7);
        $this->docParser = new DocParser();
        $this->docParser->setIgnoreNotImportedAnnotations(true);
        $this->docParser->setIgnoredAnnotationNames(['package', 'author']);
        $this->docParser->addNamespace('Plumbok\\Annotation');
        AnnotationRegistry::registerFile(__DIR__ . DIRECTORY_SEPARATOR . 'Annotation' . DIRECTORY_SEPARATOR . 'Value.php');
        AnnotationRegistry::registerFile(__DIR__ . DIRECTORY_SEPARATOR . 'Annotation' . DIRECTORY_SEPARATOR . 'Data.php');
        AnnotationRegistry::registerFile(__DIR__ . DIRECTORY_SEPARATOR . 'Annotation' . DIRECTORY_SEPARATOR . 'Getter.php');
        AnnotationRegistry::registerFile(__DIR__ . DIRECTORY_SEPARATOR . 'Annotation' . DIRECTORY_SEPARATOR . 'Setter.php');
        $this->prettyPrinter = new Standard();
        $this->docBlockSerializer = new Serializer();
    }

    /**
     * @param string $filename
     * @return string
     */
    public function compile(string $filename) : string
    {
        $nodes = $this->phpParser->parse(file_get_contents($filename));
        $oldDocBlocks = $this->findClassDocBlocks(...$nodes);
        $this->processNodes(...$nodes);
        $newDocBlocks = $this->findClassDocBlocks(...$nodes);
        $this->applyFileDocBlocks($filename, $oldDocBlocks, $newDocBlocks);

        $code = $this->prettyPrinter->prettyPrint($nodes);
//        echo $code;
        return $code;
    }

    /**
     * @param Node[] ...$nodes
     * @return Node\Stmt\Namespace_[]
     */
    private function findNamespaces(Node ...$nodes) : array
    {
        $namespaces = [];
        foreach ($nodes as $node) {
            if ($node instanceof Node\Stmt\Namespace_) {
                $namespaces[] = $node;
            }
        }

        return $namespaces;
    }

    /**
     * @param Node[] ...$nodes
     * @return string[]
     */
    private function findUses(Node ...$nodes) : array
    {
        $uses = [];
        foreach ($nodes as $node) {
            if ($node instanceof Node\Stmt\GroupUse) {
                foreach ($node->uses as $use) {
                    $uses[$use->alias] = $node->prefix->toString() . '\\' . $use->name->toString();
                }
            }
            if ($node instanceof Node\Stmt\Use_) {
                foreach ($node->uses as $use) {
                    $uses[$use->alias] = $use->name->toString();
                }
            }
        }

        return $uses;
    }

    /**
     * @param Node[] ...$nodes
     * @return Node\Stmt\Property[]
     */
    private function findProperties(Node ...$nodes) : array
    {
        $properties = [];
        foreach ($nodes as $node) {
            if ($node instanceof Node\Stmt\Property) {
                $properties[] = $node;
            }
        }

        return $properties;
    }

    /**
     * @param Node[] ...$nodes
     * @return Node\Stmt\Class_[]
     */
    private function findClasses(Node ...$nodes) : array
    {
        $classes = [];
        foreach ($nodes as $node) {
            if ($node instanceof Node\Stmt\Class_) {
                $classes[] = $node;
            }
            if (property_exists($node, 'stmts') && count($node->stmts)) {
                $classes += $this->findClasses(...$node->stmts);
            }
        }

        return $classes;
    }

    /**
     * @param string $propertyName
     * @param Type $type
     * @param TypeContext $typeContext
     * @return Statements
     */
    private function generateGetter(string $propertyName, Type $type, TypeContext $typeContext) : Statements
    {
        $generator = new GetterGenerator($this->docBlockSerializer);
        $generator->setPropertyName($propertyName);
        $generator->setType($type);
        $generator->setTypeContext($typeContext);

        return $generator->generate();
    }

    /**
     * @param string $propertyName
     * @param Type $type
     * @param TypeContext $typeContext
     * @return Statements
     */
    private function generateSetter(string $propertyName, Type $type, TypeContext $typeContext) : Statements
    {
        $generator = new SetterGenerator($this->docBlockSerializer);
        $generator->setPropertyName($propertyName);
        $generator->setType($type);
        $generator->setTypeContext($typeContext);

        return $generator->generate();
    }

    /**
     * @param Node[] ...$nodes
     */
    private function processNodes(Node ...$nodes)
    {
        $namespaces = $this->findNamespaces(...$nodes);
        foreach ($namespaces as $namespace) {
            $typeContext = new TypeContext($namespace->name->toString(), $this->findUses(...$namespace->stmts));

            $classes = $this->findClasses(...$namespace->stmts);
            foreach ($classes as $class) {
                $this->processClass($class, $typeContext);
            }
        }
        if (!$namespaces) {
            $typeContext = new TypeContext('global', $this->findUses(...$nodes));

            $classes = $this->findClasses(...$nodes);
            foreach ($classes as $class) {
                $this->processClass($class, $typeContext);
            }
        }
    }

    /**
     * @param Node\Stmt\Class_ $class
     * @param TypeContext $typeContext
     */
    private function processClass(Node\Stmt\Class_ $class, TypeContext $typeContext)
    {
        $classAnnotations = $class->getDocComment() ? $this->docParser->parse($class->getDocComment()->getText()) : [];
        $context = new Context($classAnnotations);
        if ($class->getDocComment()) {
            $classDocBlock = $this->createDocBlock($class->getDocComment()->getText(), $typeContext);
        } else {
            $classDocBlock = new DocBlock();
        }

        $statements = new Statements();
        $classProperties = $this->findProperties(...$class->stmts);

        foreach ($classProperties as $property) {
            if ($property->getDocComment()) {
                $propertyDocComment = $property->getDocComment()->getText();
                $propertyAnnotations = $this->docParser->parse($propertyDocComment);
                $propertyDocBlock = $this->createDocBlock($propertyDocComment, $typeContext);
            } else {
                $propertyAnnotations = [];
                $propertyDocBlock = new DocBlock();
            }

            $varTags = $propertyDocBlock->getTagsByName('var');
            $type = count($varTags) ? $varTags[0]->getType() : new Mixed();

            foreach ($property->props as $prop) {
                if ($context->isAllPropertyGetters()) {
                    $statements->merge($this->generateGetter($prop->name, $type, $typeContext));
                }
                if ($context->isAllPropertySetters()) {
                    $statements->merge($this->generateSetter($prop->name, $type, $typeContext));
                }
                foreach ($propertyAnnotations as $annotation) {
                    if ($annotation instanceof Getter && !$context->isAllPropertyGetters()) {
                        $statements->merge($this->generateGetter($prop->name, $type, $typeContext));
                    }
                    if ($annotation instanceof Setter && !$context->isAllPropertySetters()) {
                        $statements->merge($this->generateSetter($prop->name, $type, $typeContext));
                    }
                }
            }
        }
        if ($context->isAllArgsConstructor()) {
//                    $statements->merge($this->generateAllArgsConstructor($property->props));
        }

        $tags = $classDocBlock->getTags();
        $docBlockFactory = function ($docBlock) use ($typeContext) : DocBlock {
            return $this->createDocBlock($docBlock, $typeContext);
        };
        foreach (Tags::createFromStatements($statements, $docBlockFactory) as $tag) {
            $tags[] = $tag;
        }
        $class->setDocComment($this->createDocComment($classDocBlock, ...$tags));
        foreach ($statements as $statement) {
            $class->stmts[] = $statement;
        }
    }

    /**
     * @param DocBlock $docBlock
     * @param DocBlock\Tag[] ...$tags
     * @return Doc
     */
    private function createDocComment(DocBlock $docBlock, DocBlock\Tag ...$tags) : Doc
    {
        $docComment = $this->docBlockSerializer->getDocComment(new DocBlock(
            $docBlock->getSummary(),
            $docBlock->getDescription(),
            $tags
        ));

        return new Doc(str_replace("/**\n * \n *\n", "/**\n", $docComment));
    }

    /**
     * @param $docBlock
     * @param TypeContext $typeContext
     * @return DocBlock
     */
    private function createDocBlock($docBlock, TypeContext $typeContext) : DocBlock
    {
        return DocBlockFactory::createInstance()->create($docBlock, $typeContext);
    }

    /**
     * @param Node[] ...$nodes
     * @return array
     */
    private function findClassDocBlocks(Node ...$nodes) : array
    {
        $docBlocks = [];
        $namespaces = $this->findNamespaces(...$nodes);
        foreach ($namespaces as $namespace) {
            $classes = $this->findClasses(...$namespace->stmts);
            foreach ($classes as $class) {
                $docBlocks["{$namespace->name}\\{$class->name}"] = [
                    'line' => $class->getLine(),
                    'docBlock' => $class->getDocComment(),
                ];
            }
        }
        if (!$namespaces) {
            $classes = $this->findClasses(...$nodes);
            foreach ($classes as $class) {
                $docBlocks[$class->name] = [
                    'line' => $class->getLine(),
                    'docBlock' => $class->getDocComment(),
                ];
            }
        }

        return $docBlocks;
    }

    /**
     * @param string $filename
     * @param array $oldDocBlocks
     * @param array $newDocBlocks
     */
    private function applyFileDocBlocks(string $filename, array $oldDocBlocks, array $newDocBlocks)
    {
        $fp = fopen($filename, 'rw+');
        $fragments = [];
        foreach ($oldDocBlocks as $className => $docBlockPosition) {
            /** @var Doc $docComment */
            $docComment = $docBlockPosition['docBlock'];
            /** @var Doc $newDocComment */
            $newDocComment = $newDocBlocks[$className]['docBlock'];
            if ($docComment->getFilePos()) {
                $fragments[] = fread($fp, $docComment->getFilePos() - ftell($fp));
                $fragments[] = $newDocComment->getText();
                fseek($fp, strlen($docComment->getText()), SEEK_CUR);
            }
        }
        $fragments[] = fread($fp, filesize($filename) - ftell($fp));
        file_put_contents($filename, implode($fragments));
    }
}
