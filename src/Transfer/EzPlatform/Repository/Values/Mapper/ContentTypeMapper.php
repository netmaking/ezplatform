<?php

/**
 * This file is part of Transfer.
 *
 * For the full copyright and license information, please view the LICENSE file located
 * in the root directory.
 */

namespace Transfer\EzPlatform\Repository\Values\Mapper;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeCreateStruct;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeUpdateStruct;
use Transfer\EzPlatform\Repository\Values\ContentTypeObject;

/**
 * Contenttype mapper.
 *
 * @internal
 *
 * @author Harald Tollefsen <harald@netmaking.no>
 */
class ContentTypeMapper
{
    /**
     * @var ContentTypeObject
     */
    public $contentTypeObject;

    /**
     * @var ContentTypeService
     */
    public $contentTypeService;

    /**
     * @param ContentTypeObject $contentTypeObject
     */
    public function __construct(ContentTypeObject $contentTypeObject)
    {
        $this->contentTypeObject = &$contentTypeObject;
    }

    /**
     * @param ContentType $contentType
     */
    public function contentTypeToObject(ContentType $contentType)
    {
        $this->contentTypeObject->data['identifier'] = $contentType->identifier;
        $this->contentTypeObject->data['names'] = $contentType->getNames();
        $this->contentTypeObject->data['descriptions'] = $contentType->getDescriptions();
        $this->contentTypeObject->data['name_schema'] = $contentType->nameSchema;
        $this->contentTypeObject->data['url_alias_schema'] = $contentType->urlAliasSchema;
        $this->contentTypeObject->data['is_container'] = $contentType->isContainer;
        $this->contentTypeObject->data['default_always_available'] = $contentType->defaultAlwaysAvailable;
        $this->contentTypeObject->data['default_sort_field'] = $contentType->defaultSortField;
        $this->contentTypeObject->data['default_sort_order'] = $contentType->defaultSortOrder;

        $this->contentTypeObject->setProperty('id', $contentType->id);
        $this->contentTypeObject->setProperty('content_type_groups', $contentType->contentTypeGroups);
    }

    /**
     * @param ContentTypeCreateStruct $createStruct
     */
    public function mapObjectToCreateStruct(ContentTypeCreateStruct $createStruct)
    {
        $createStruct->remoteId = sha1(microtime());

        // Name collection (ez => transfer)
        $keys = array(
            'names' => 'names',
            'descriptions' => 'descriptions',
            'mainLanguageCode' => 'main_language_code',
            'nameSchema' => 'name_schema',
            'urlAliasSchema' => 'url_alias_schema',
            'isContainer' => 'is_container',
            'defaultAlwaysAvailable' => 'default_always_available',
            'defaultSortField' => 'default_sort_field',
            'defaultSortOrder' => 'default_sort_order',
        );

        $this->arrayToStruct($createStruct, $keys);

        $this->callStruct($createStruct);
    }

    /**
     * @param ContentTypeUpdateStruct $updateStruct
     */
    public function mapObjectToUpdateStruct(ContentTypeUpdateStruct $updateStruct)
    {
        // Name collection (ez => transfer)
        $keys = array(
            'names' => 'names',
            'descriptions' => 'descriptions',
            'mainLanguageCode' => 'main_language_code',
            'nameSchema' => 'name_schema',
            'urlAliasSchema' => 'url_alias_schema',
            'isContainer' => 'is_container',
            'defaultAlwaysAvailable' => 'default_always_available',
            'defaultSortField' => 'default_sort_field',
            'defaultSortOrder' => 'default_sort_order',
        );

        $this->arrayToStruct($updateStruct, $keys);

        $this->callStruct($updateStruct);
    }

    /**
     * @param ContentTypeCreateStruct|ContentTypeUpdateStruct $struct
     * @param array                                           $keys
     */
    private function arrayToStruct($struct, $keys)
    {
        foreach ($keys as $ezKey => $transferKey) {
            if (isset($this->contentTypeObject->data[$transferKey])) {
                $struct->$ezKey = $this->contentTypeObject->data[$transferKey];
            }
        }
    }

    /**
     * @param ContentTypeCreateStruct|ContentTypeUpdateStruct $struct
     */
    private function callStruct($struct)
    {
        if ($this->contentTypeObject->getProperty('struct_callback')) {
            $callback = $this->contentTypeObject->getProperty('struct_callback');
            $callback($struct);
        }
    }
}
