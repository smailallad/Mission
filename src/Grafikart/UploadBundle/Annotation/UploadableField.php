<?php

namespace Grafikart\UploadBundle\Annotation;

use Doctrine\common\Annotaions\Annotation\Target;

/**
* @Annotation
* @Target("PROPERTY")
*/
class UploadableField {
		
		/**
		* @var string
		*/
		private $filename;

		/**
		* @var string
		*/
		private $path;
		
		
		public function __construct(array $options) {
			
			if (empty($options['filename'])) {
				throw new \InvalidArgumentException("L'annotation UploadableField doit avoir un attribut 'filename'"); 
			}
			
			if (empty($options['path'])) {
				throw new \InvalidArgumentException("L'annotation UploadableField doit avoir un attribut 'path'");
			}
			
			$this->filename = $options['filename'];
			$this->path = $options['path'];
		
		}
		
		public function getFilename() {
			
			return $this->filename;
		
		}
		
		public function getPath() {
			
			return $this->path;
		
		}

}

