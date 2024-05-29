<?php

namespace common\moy_sklad\entities;

use yii\web\NotAcceptableHttpException as NotAcceptable;

/**
 * Счет покупателю
 *
 * @link https://dev.moysklad.ru/doc/api/remap/1.2/documents/#dokumenty-schet-pokupatelu
 */
class Invoiceout extends _Entity
{
    const NAME = 'invoiceout';

    private $data;

    public function __construct(array $data)
    {
        if (empty($data['name']))
            throw new NotAcceptable('"name" is required');
        if (empty($data['organization']['id']))
            throw new NotAcceptable('"organization.id" is required');
        if (empty($data['counterparty']['id']))
            throw new NotAcceptable('"agent.id" is required');

        $this->data = $data;
    }

    public function buildFields()
    {
        $fields = [
            'name' => $this->data['name'],
        ];

        return array_merge(
            $fields,
            $this->_buildOrganizationField(),
            $this->_buildAgentField()
        );
    }

    private function _buildOrganizationField()
    {
        return [
            'organization' => [
                'meta' => [
                    'href' => self::url('/organization/' . $this->data['organization']['id']),
                    'metadataHref' => self::url('/organization/metadata'),
                    'type' => 'organization',
                    'mediaType' => 'application/json'
                ]
            ]
        ];
    }

    private function _buildAgentField()
    {
        return [
            'agent' => [
                'meta' => [
                    'href' => self::url('/counterparty/' . $this->data['counterparty']['id']),
                    'metadataHref' => self::url('/counterparty/metadata'),
                    'type' => 'counterparty',
                    'mediaType' => 'application/json'
                ]
            ]
        ];
    }
}