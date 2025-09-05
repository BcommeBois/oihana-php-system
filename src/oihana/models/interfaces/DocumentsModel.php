<?php

namespace oihana\models\interfaces;

/**
 * @package oihana\models\interfaces
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
interface DocumentsModel
  extends CountModel    ,
          DeleteModel   ,
          ExistModel    ,
          GetModel      ,
          InsertModel   ,
          LastModel     ,
          ListModel     ,
          ReplaceModel  ,
          UpdateModel   ,
          UpsertModel   ,
          TruncateModel
          {}