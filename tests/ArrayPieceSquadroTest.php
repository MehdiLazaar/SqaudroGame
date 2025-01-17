<?php
use PHPUnit\Framework\TestCase;
use src\ArrayPieceSquadro;
use src\PieceSquadro;



require_once '../src/PieceSquadro.php';
require __DIR__ . '/../vendor/autoload.php';
class ArrayPieceSquadroTest extends TestCase {
    private ArrayPieceSquadro $arrayPieceSquadro;

    protected function setUp(): void {
        // Crée un nouvel objet ArrayPieceSquadro pour chaque test
        $this->arrayPieceSquadro = new ArrayPieceSquadro();
    }

    public function testAddPiece(): void {
        $piece = PieceSquadro::initBlancEst();
        $this->arrayPieceSquadro->add($piece);

        $this->assertCount(1, $this->arrayPieceSquadro);
        $this->assertSame($piece, $this->arrayPieceSquadro[0]);
    }

    public function testRemovePiece(): void {
        $piece = PieceSquadro::initBlancEst();
        $this->arrayPieceSquadro->add($piece);

        $this->arrayPieceSquadro->remove(0);

        $this->assertCount(0, $this->arrayPieceSquadro);
    }

    public function testRemoveInvalidIndexThrowsException(): void {
        $this->expectException(OutOfBoundsException::class);
        $this->arrayPieceSquadro->remove(99);
    }

    public function testOffsetSetAndGet(): void {
        $piece1 = PieceSquadro::initBlancEst();
        $piece2 = PieceSquadro::initBlancOuest();

        $this->arrayPieceSquadro[0] = $piece1;
        $this->arrayPieceSquadro[1] = $piece2;

        $this->assertSame($piece1, $this->arrayPieceSquadro[0]);
        $this->assertSame($piece2, $this->arrayPieceSquadro[1]);
    }

    public function testOffsetUnset(): void {
        $piece = PieceSquadro::initBlancEst();
        $this->arrayPieceSquadro->add($piece);

        unset($this->arrayPieceSquadro[0]);

        $this->assertCount(0, $this->arrayPieceSquadro);
    }

    public function testOffsetSetInvalidValueThrowsException(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->arrayPieceSquadro[0] = "not a PieceSquadro";
    }

    public function testToJson(): void {
        $piece = PieceSquadro::initBlancEst();
        $this->arrayPieceSquadro->add($piece);

        $json = $this->arrayPieceSquadro->toJson();

        $this->assertJson($json);
        $this->assertStringContainsString('"couleur":0', $json); // Exemple de vérification
    }

    public function testFromJson(): void {
        $json = '[{"couleur":0,"direction":1}]';
        $arrayPieceSquadro = ArrayPieceSquadro::fromJson($json);

        $this->assertCount(1, $arrayPieceSquadro);
        $this->assertInstanceOf(PieceSquadro::class, $arrayPieceSquadro[0]);
    }

    public function testCountable(): void {
        $this->assertCount(0, $this->arrayPieceSquadro);

        $this->arrayPieceSquadro->add(PieceSquadro::initBlancEst());
        $this->assertCount(1, $this->arrayPieceSquadro);
    }

    public function testToString(): void {
        $piece = PieceSquadro::initBlancEst();
        $this->arrayPieceSquadro->add($piece);

        $string = (string) $this->arrayPieceSquadro;

        $this->assertStringContainsString('PieceSquadro', $string);
    }
}