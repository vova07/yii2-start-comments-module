<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * CLass m140911_074715_create_module_tbl
 * @package vova07\comments\migrations
 *
 * Create module tables.
 *
 * Will be created 1 table:
 * - `{{%comments}}` - Comments table.
 */
class m140911_074715_create_module_tbl extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // MySql table options
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        // Comment models table
        $this->createTable('{{%comments_models}}', [
            'id' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL PRIMARY KEY',
            'name' => Schema::TYPE_STRING . '(255) NOT NULL',
            'status_id' => 'tinyint(1) NOT NULL DEFAULT 1',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL'
        ], $tableOptions);

        $this->createIndex('name', '{{%comments_models}}', 'name');
        $this->createIndex('status_id', '{{%comments_models}}', 'status_id');
        $this->createIndex('created_at', '{{%comments_models}}', 'created_at');
        $this->createIndex('updated_at', '{{%comments_models}}', 'updated_at');

        // Comments table
        $this->createTable('{{%comments}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER,
            'model_class' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
            'model_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'author_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'content' => Schema::TYPE_TEXT . ' NOT NULL',
            'status_id' => 'tinyint(2) NOT NULL DEFAULT 0',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL'
        ], $tableOptions);

        // Indexes
        $this->createIndex('status_id', '{{%comments}}', 'status_id');
        $this->createIndex('created_at', '{{%comments}}', 'created_at');
        $this->createIndex('updated_at', '{{%comments}}', 'updated_at');

        // Foreign Keys
        $this->addForeignKey('FK_comment_parent', '{{%comments}}', 'parent_id', '{{%comments}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK_comment_author', '{{%comments}}', 'author_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK_comment_model_class', '{{%comments}}', 'model_class', '{{%comments_models}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%comments}}');
        $this->dropTable('{{%comments_models}}');
    }
}
