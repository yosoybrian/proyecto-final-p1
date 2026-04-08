<?php

namespace Tests\Helpers;

/**
 * Clase auxiliar para validar archivos PlantUML
 */
class PlantUMLValidator
{
    private string $content;
    private array $lines;
    
    public function __construct(string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new \Exception("Archivo no encontrado: {$filePath}");
        }
        
        $this->content = file_get_contents($filePath);
        $this->lines = explode("\n", $this->content);
    }
    
    /**
     * Verifica si el archivo comienza y termina correctamente con @startuml/@enduml
     */
    public function hasValidStructure(): bool
    {
        return str_contains($this->content, '@startuml') && 
               str_contains($this->content, '@enduml');
    }
    
    /**
     * Cuenta cuántas clases están definidas
     */
    public function countClasses(): int
    {
        return preg_match_all('/^(?:abstract\s+)?(?:class|interface)\s+\w+/m', $this->content);
    }
    
    /**
     * Verifica si existe una clase (abstracta o no)
     */
    public function hasClass(string $className): bool
    {
        // Soporta: class ClassName { o class ClassName extends/implements
        return preg_match('/(?:abstract\s+)?(?:class|interface)\s+' . preg_quote($className, '/') . '\s*(?:[{\n]|extends|implements)/m', $this->content) === 1;
    }
    
    /**
     * Verifica si existe una clase abstracta
     */
    public function hasAbstractClass(string $className): bool
    {
        // Soporta: abstract class ClassName { o abstract class ClassName extends/implements
        return preg_match('/abstract\s+class\s+' . preg_quote($className, '/') . '\s*(?:[{\n]|extends|implements)/m', $this->content) === 1;
    }
    
    /**
     * Verifica si existe una interfaz
     */
    public function hasInterface(string $interfaceName): bool
    {
        return preg_match('/interface\s+' . preg_quote($interfaceName, '/') . '\s*[{\n]/m', $this->content) === 1;
    }
    
    /**
     * Verifica si una clase tiene un atributo específico con visibilidad
     */
    public function classHasAttribute(string $className, string $attribute, string $visibility = '-'): bool
    {
        // Buscar la clase y luego el atributo dentro de ella
        // Soporta: class ClassName { o class ClassName extends/implements ... {
        $pattern = '/(?:abstract\s+)?(?:class|interface)\s+' . preg_quote($className, '/') . 
                   '(?:\s+(?:extends|implements)\s+\w+(?:\s+(?:extends|implements)\s+\w+)?)?\s*\{[^}]*' . 
                   preg_quote($visibility, '/') . '\s*' . preg_quote($attribute, '/') . '\s*:/s';
        return preg_match($pattern, $this->content) === 1;
    }
    
    /**
     * Verifica si una clase tiene un método específico
     */
    public function classHasMethod(string $className, string $method, string $visibility = '+'): bool
    {
        // Soporta: class ClassName { o class ClassName extends/implements ... {
        $pattern = '/(?:abstract\s+)?(?:class|interface)\s+' . preg_quote($className, '/') . 
                   '(?:\s+(?:extends|implements)\s+\w+(?:\s+(?:extends|implements)\s+\w+)?)?\s*\{[^}]*' . 
                   preg_quote($visibility, '/') . '\s*(?:\{abstract\}\s*)?' . preg_quote($method, '/') . '\s*\(/s';
        return preg_match($pattern, $this->content) === 1;
    }
    
    /**
     * Cuenta cuántas relaciones de un tipo específico existen
     */
    public function countRelations(string $relationType = '--'): int
    {
        $escaped = preg_quote($relationType, '/');
        return preg_match_all('/' . $escaped . '/m', $this->content);
    }
    
    /**
     * Verifica si existe una relación específica entre dos clases
     */
    public function hasRelation(string $class1, string $class2, string $relationType = '--'): bool
    {
        $pattern1 = '/' . preg_quote($class1, '/') . '.*' . preg_quote($relationType, '/') . '.*' . preg_quote($class2, '/') . '/m';
        $pattern2 = '/' . preg_quote($class2, '/') . '.*' . preg_quote($relationType, '/') . '.*' . preg_quote($class1, '/') . '/m';
        
        return preg_match($pattern1, $this->content) === 1 || 
               preg_match($pattern2, $this->content) === 1;
    }
    
    /**
     * Verifica si existe una herencia específica
     */
    public function hasInheritance(string $childClass, string $parentClass): bool
    {
        // Busca: ClaseHija <|-- ClasePadre o ClasePadre <|-- ClaseHija (dependiendo de la dirección)
        $pattern1 = '/' . preg_quote($childClass, '/') . '\s*<\|--\s*' . preg_quote($parentClass, '/') . '/m';
        $pattern2 = '/' . preg_quote($parentClass, '/') . '\s*<\|--\s*' . preg_quote($childClass, '/') . '/m';
        
        return preg_match($pattern1, $this->content) === 1 || 
               preg_match($pattern2, $this->content) === 1;
    }
    
    /**
     * Verifica si existe una implementación de interfaz
     */
    public function hasInterfaceImplementation(string $className, string $interfaceName): bool
    {
        $pattern1 = '/' . preg_quote($className, '/') . '\s*<\|\.\.\s*' . preg_quote($interfaceName, '/') . '/m';
        $pattern2 = '/' . preg_quote($interfaceName, '/') . '\s*<\|\.\.\s*' . preg_quote($className, '/') . '/m';
        
        return preg_match($pattern1, $this->content) === 1 || 
               preg_match($pattern2, $this->content) === 1;
    }
    
    /**
     * Verifica si tiene una nota específica
     */
    public function hasNote(string $noteText): bool
    {
        return str_contains($this->content, $noteText);
    }
    
    /**
     * Verifica si tiene configuración skinparam
     */
    public function hasSkinparam(string $param): bool
    {
        return preg_match('/skinparam\s+' . preg_quote($param, '/') . '/m', $this->content) === 1;
    }
    
    /**
     * Verifica si tiene comentarios
     */
    public function hasComments(): bool
    {
        return str_contains($this->content, "//") || str_contains($this->content, "'");
    }
    
    /**
     * Cuenta el número de notas
     */
    public function countNotes(): int
    {
        return preg_match_all('/note\s+(right|left|top|bottom)\s+of\s+\w+/m', $this->content);
    }
    
    /**
     * Verifica multiplicidad en una relación
     */
    public function hasMultiplicity(string $multiplicity): bool
    {
        return str_contains($this->content, '"' . $multiplicity . '"');
    }
    
    /**
     * Obtiene el contenido completo
     */
    public function getContent(): string
    {
        return $this->content;
    }
}
