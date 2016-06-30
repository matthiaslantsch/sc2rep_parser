<?php
/**
 * Copyright (c) 2016, Rogiel Sulzbach
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 * this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation
 * and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF
 * THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace Rogiel\MPQ\Metadata;

use Rogiel\MPQ\Stream\Parser\BinaryStreamParser;

class Hash {

	// The hash of the full file name (part A)
	private $name1;

	// The hash of the full file name (part B)
	private $name2;

	// The language of the file. This is a Windows LANGID data type, and uses the same values.
	// 0 indicates the default language (American English), or that the file is language-neutral.
	private $locale;

	// The platform the file is used for. 0 indicates the default platform.
	// No other values have been observed.
	private $platform;

	// If the hash table entry is valid, this is the index into the block table of the file.
	// Otherwise, one of the following two values:
	//  - FFFFFFFFh: Hash table entry is empty, and has always been empty.
	//               Terminates searches for a given file.
	//  - FFFFFFFEh: Hash table entry is empty, but was valid at some point (a deleted file).
	//               Does not terminate searches for a given file.
	private $blockIndex;

	// -----------------------------------------------------------------------------------------------------------------

	public static function parse(BinaryStreamParser $parser) {
		$hash = new Hash();

		$hash->name1 = $parser->readUInt32();
		$hash->name2 = $parser->readUInt32();
		$hash->locale = $parser->readUInt16();
		$hash->platform = $parser->readUInt16();
		$hash->blockIndex = $parser->readUInt32();

		return $hash;
	}

	// -----------------------------------------------------------------------------------------------------------------

	/**
	 * @return mixed
	 */
	public function getName1() {
		return $this->name1;
	}

	/**
	 * @return mixed
	 */
	public function getName2() {
		return $this->name2;
	}

	/**
	 * @return mixed
	 */
	public function getLocale() {
		return $this->locale;
	}

	/**
	 * @return mixed
	 */
	public function getPlatform() {
		return $this->platform;
	}

	/**
	 * @return mixed
	 */
	public function getBlockIndex() {
		return $this->blockIndex;
	}

}