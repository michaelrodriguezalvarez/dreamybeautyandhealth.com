<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210606051930 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE administrador (id INT AUTO_INCREMENT NOT NULL, foto INT DEFAULT NULL, usuario INT DEFAULT NULL, nombre VARCHAR(255) NOT NULL, apellidos VARCHAR(255) DEFAULT NULL, INDEX fk_administrador_usuario (usuario), INDEX fk_administrador_foto (foto), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE beneficio (id INT AUTO_INCREMENT NOT NULL, imagen INT DEFAULT NULL, servicio INT DEFAULT NULL, nombre VARCHAR(255) NOT NULL, descripcion VARCHAR(255) DEFAULT NULL, INDEX fk_beneficio_imagen (imagen), INDEX fk_beneficio_servicio (servicio), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cita (id INT AUTO_INCREMENT NOT NULL, especialidad INT DEFAULT NULL, paciente INT DEFAULT NULL, paquete INT DEFAULT NULL, servicio INT DEFAULT NULL, fecha DATETIME NOT NULL, estado VARCHAR(255) NOT NULL, INDEX fk_cita_servicio (servicio), INDEX fk_cita_paciente (paciente), INDEX fk_cita_especialidad (especialidad), INDEX fk_cita_paquete (paquete), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE especialidad (id INT AUTO_INCREMENT NOT NULL, imagen INT DEFAULT NULL, nombre VARCHAR(255) NOT NULL, descripcion VARCHAR(255) DEFAULT NULL, INDEX fk_especialidad_imagen (imagen), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE especialista (id INT AUTO_INCREMENT NOT NULL, especialidad INT DEFAULT NULL, foto INT DEFAULT NULL, usuario INT DEFAULT NULL, nombre VARCHAR(255) NOT NULL, apellidos VARCHAR(255) NOT NULL, nacionalidad VARCHAR(255) DEFAULT NULL, INDEX fk_especialista_especialidad (especialidad), INDEX fk_especialista_usuario (usuario), INDEX fk_especialista_foto (foto), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE imagen (id INT AUTO_INCREMENT NOT NULL, imagen VARCHAR(255) NOT NULL, nombre VARCHAR(255) NOT NULL, descripcion TEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paciente (id INT AUTO_INCREMENT NOT NULL, foto INT DEFAULT NULL, usuario INT DEFAULT NULL, nombre VARCHAR(255) NOT NULL, apellidos VARCHAR(255) NOT NULL, telefono VARCHAR(255) DEFAULT NULL, direccion TEXT DEFAULT NULL, INDEX fk_paciente_usuario (usuario), INDEX fk_paciente_foto (foto), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paquete (id INT AUTO_INCREMENT NOT NULL, imagen INT DEFAULT NULL, nombre VARCHAR(255) NOT NULL, descripcion VARCHAR(255) DEFAULT NULL, INDEX fk_paquete_imagen (imagen), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paqueteservicio (id INT AUTO_INCREMENT NOT NULL, paquete INT DEFAULT NULL, servicio INT DEFAULT NULL, INDEX fk_paqueteservicio_paquete (paquete), INDEX fk_paqueteservicio_servicio (servicio), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE servicio (id INT AUTO_INCREMENT NOT NULL, especialidad INT DEFAULT NULL, imagen INT DEFAULT NULL, nombre VARCHAR(255) NOT NULL, descripcion VARCHAR(255) DEFAULT NULL, INDEX fk_servicio_imagen (imagen), INDEX fk_servicio_especialidad (especialidad), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE administrador ADD CONSTRAINT FK_44F9A521EADC3BE5 FOREIGN KEY (foto) REFERENCES imagen (id)');
        $this->addSql('ALTER TABLE administrador ADD CONSTRAINT FK_44F9A5212265B05D FOREIGN KEY (usuario) REFERENCES user (id)');
        $this->addSql('ALTER TABLE beneficio ADD CONSTRAINT FK_FCD859388319D2B3 FOREIGN KEY (imagen) REFERENCES imagen (id)');
        $this->addSql('ALTER TABLE beneficio ADD CONSTRAINT FK_FCD85938CB86F22A FOREIGN KEY (servicio) REFERENCES servicio (id)');
        $this->addSql('ALTER TABLE cita ADD CONSTRAINT FK_3E379A62ACB064F9 FOREIGN KEY (especialidad) REFERENCES especialidad (id)');
        $this->addSql('ALTER TABLE cita ADD CONSTRAINT FK_3E379A62C6CBA95E FOREIGN KEY (paciente) REFERENCES paciente (id)');
        $this->addSql('ALTER TABLE cita ADD CONSTRAINT FK_3E379A6212686A95 FOREIGN KEY (paquete) REFERENCES paquete (id)');
        $this->addSql('ALTER TABLE cita ADD CONSTRAINT FK_3E379A62CB86F22A FOREIGN KEY (servicio) REFERENCES servicio (id)');
        $this->addSql('ALTER TABLE especialidad ADD CONSTRAINT FK_ACB064F98319D2B3 FOREIGN KEY (imagen) REFERENCES imagen (id)');
        $this->addSql('ALTER TABLE especialista ADD CONSTRAINT FK_F206C397ACB064F9 FOREIGN KEY (especialidad) REFERENCES especialidad (id)');
        $this->addSql('ALTER TABLE especialista ADD CONSTRAINT FK_F206C397EADC3BE5 FOREIGN KEY (foto) REFERENCES imagen (id)');
        $this->addSql('ALTER TABLE especialista ADD CONSTRAINT FK_F206C3972265B05D FOREIGN KEY (usuario) REFERENCES user (id)');
        $this->addSql('ALTER TABLE paciente ADD CONSTRAINT FK_C6CBA95EEADC3BE5 FOREIGN KEY (foto) REFERENCES imagen (id)');
        $this->addSql('ALTER TABLE paciente ADD CONSTRAINT FK_C6CBA95E2265B05D FOREIGN KEY (usuario) REFERENCES user (id)');
        $this->addSql('ALTER TABLE paquete ADD CONSTRAINT FK_12686A958319D2B3 FOREIGN KEY (imagen) REFERENCES imagen (id)');
        $this->addSql('ALTER TABLE paqueteservicio ADD CONSTRAINT FK_7DA712C112686A95 FOREIGN KEY (paquete) REFERENCES paquete (id)');
        $this->addSql('ALTER TABLE paqueteservicio ADD CONSTRAINT FK_7DA712C1CB86F22A FOREIGN KEY (servicio) REFERENCES servicio (id)');
        $this->addSql('ALTER TABLE servicio ADD CONSTRAINT FK_CB86F22AACB064F9 FOREIGN KEY (especialidad) REFERENCES especialidad (id)');
        $this->addSql('ALTER TABLE servicio ADD CONSTRAINT FK_CB86F22A8319D2B3 FOREIGN KEY (imagen) REFERENCES imagen (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cita DROP FOREIGN KEY FK_3E379A62ACB064F9');
        $this->addSql('ALTER TABLE especialista DROP FOREIGN KEY FK_F206C397ACB064F9');
        $this->addSql('ALTER TABLE servicio DROP FOREIGN KEY FK_CB86F22AACB064F9');
        $this->addSql('ALTER TABLE administrador DROP FOREIGN KEY FK_44F9A521EADC3BE5');
        $this->addSql('ALTER TABLE beneficio DROP FOREIGN KEY FK_FCD859388319D2B3');
        $this->addSql('ALTER TABLE especialidad DROP FOREIGN KEY FK_ACB064F98319D2B3');
        $this->addSql('ALTER TABLE especialista DROP FOREIGN KEY FK_F206C397EADC3BE5');
        $this->addSql('ALTER TABLE paciente DROP FOREIGN KEY FK_C6CBA95EEADC3BE5');
        $this->addSql('ALTER TABLE paquete DROP FOREIGN KEY FK_12686A958319D2B3');
        $this->addSql('ALTER TABLE servicio DROP FOREIGN KEY FK_CB86F22A8319D2B3');
        $this->addSql('ALTER TABLE cita DROP FOREIGN KEY FK_3E379A62C6CBA95E');
        $this->addSql('ALTER TABLE cita DROP FOREIGN KEY FK_3E379A6212686A95');
        $this->addSql('ALTER TABLE paqueteservicio DROP FOREIGN KEY FK_7DA712C112686A95');
        $this->addSql('ALTER TABLE beneficio DROP FOREIGN KEY FK_FCD85938CB86F22A');
        $this->addSql('ALTER TABLE cita DROP FOREIGN KEY FK_3E379A62CB86F22A');
        $this->addSql('ALTER TABLE paqueteservicio DROP FOREIGN KEY FK_7DA712C1CB86F22A');
        $this->addSql('DROP TABLE administrador');
        $this->addSql('DROP TABLE beneficio');
        $this->addSql('DROP TABLE cita');
        $this->addSql('DROP TABLE especialidad');
        $this->addSql('DROP TABLE especialista');
        $this->addSql('DROP TABLE imagen');
        $this->addSql('DROP TABLE paciente');
        $this->addSql('DROP TABLE paquete');
        $this->addSql('DROP TABLE paqueteservicio');
        $this->addSql('DROP TABLE servicio');
    }
}
