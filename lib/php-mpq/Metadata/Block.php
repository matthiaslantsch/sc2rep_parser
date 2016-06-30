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

class Block {

	/**
	 * File is compressed using PKWARE Data compression library
	 */
	const FLAG_IMPLODE = 0x00000100;
	/**
	 * File is compressed using combination of compression methods
	 */
	const FLAG_COMPRESS = 0x00000200;
	/**
	 * The file is encrypted
	 */
	const FLAG_ENCRYPTED = 0x00010000;

	/**
	 * The decryption key for the file is altered according to the position of the file in the archive
	 */
	const FLAG_FIX_KEY = 0x00020000;

	/**
	 * The file contains incremental patch for an existing file in base MPQ
	 */
	const FLAG_PATCH_FILE =	0x00100000;

	/**
	 * Instead of being divided to 0x1000-bytes blocks, the file is stored as single unit
	 */
	const FLAG_SINGLE_UNIT = 0x01000000;
	/**
	 * File is a deletion marker, indicating that the file no longer exists. This is used to allow patch archives to delete files present in lower-priority archives in the search chain. The file usually has length of 0 or 1 byte and its name is a hash
	 */
	const FLAG_DELETE_MARKER = 0x02000000;
	/**
	 * File has checksums for each sector (explained in the File Data section). Ignored if file is not compressed or imploded.
	 */
	const FLAG_SECTOR_CRC =	0x04000000;
	/**
	 * Set if file exists, reset when the file was deleted
	 */
	const FLAG_EXISTS =	0x80000000;

	// -----------------------------------------------------------------------------------------------------------------

	// Offset of the beginning of the file data, relative to the beginning of the archive.
	private $filePos;

	// Compressed file size
	private $compressedSize;

	// Size of uncompressed file
	private $size;

	// Flags for the file. See the table below for more informations
	private $flags;

	// -----------------------------------------------------------------------------------------------------------------

	public static function parse(BinaryStreamParser $parser) {
		$block = new Block();

		$block->filePos = $parser->readUInt32();
		$block->compressedSize = $parser->readUInt32();
		$block->size = $parser->readUInt32();
		$block->flags = $parser->readUInt32();

		return $block;
	}

	// -----------------------------------------------------------------------------------------------------------------

	/**
	 * @return mixed
	 */
	public function getFilePos() {
		return $this->filePos;
	}

	/**
	 * @return mixed
	 */
	public function getCompressedSize() {
		return $this->compressedSize;
	}

	/**
	 * @return mixed
	 */
	public function getSize() {
		return $this->size;
	}

	/**
	 * @return mixed
	 */
	public function getFlags() {
		return $this->flags;
	}

	// -----------------------------------------------------------------------------------------------------------------

	public function isImploded() {
		return ($this->flags & self::FLAG_IMPLODE) != 0;
	}

	public function isCompressed() {
		return ($this->flags & self::FLAG_COMPRESS) != 0;
	}

	public function isEncrypted() {
		return ($this->flags & self::FLAG_ENCRYPTED) != 0;
	}

	public function isKeyBasedOnPosition() {
		return ($this->flags & self::FLAG_FIX_KEY) != 0;
	}

	public function isPatched() {
		return ($this->flags & self::FLAG_PATCH_FILE) != 0;
	}

	public function isSingleUnit() {
		return ($this->flags & self::FLAG_SINGLE_UNIT) != 0;
	}

	public function isDeleted() {
		return ($this->flags & self::FLAG_DELETE_MARKER) != 0;
	}

	public function isChecksumed() {
		return ($this->flags & self::FLAG_SECTOR_CRC) != 0;
	}

	public function isExisting() {
		return ($this->flags & self::FLAG_EXISTS) != 0;
	}


}