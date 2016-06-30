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

class Header {

	const ARCHIVE_FORMAT_1 = 0;
	const ARCHIVE_FORMAT_2 = 1;
	const ARCHIVE_FORMAT_3 = 2;
	const ARCHIVE_FORMAT_4 = 3;

	// Size of the archive header
	private $size;

	// Size of MPQ archive
	// This field is deprecated in the Burning Crusade MoPaQ format, and the size of the archive
	// is calculated as the size from the beginning of the archive to the end of the hash table,
	// block table, or extended block table (whichever is largest).
	private $archiveSize;

	// 0 = Format 1 (up to The Burning Crusade)
	// 1 = Format 2 (The Burning Crusade and newer)
	// 2 = Format 3 (WoW - Cataclysm beta or newer)
	// 3 = Format 4 (WoW - Cataclysm beta or newer)
	private $formatVersion;

	// Power of two exponent specifying the number of 512-byte disk sectors in each logical sector
	// in the archive. The size of each logical sector in the archive is 512 * 2^wBlockSize.
	private $blockSize;

	// Offset to the beginning of the hash table, relative to the beginning of the archive.
	private $hashTablePos;

	// Offset to the beginning of the block table, relative to the beginning of the archive.
	private $blockTablePos;

	// Number of entries in the hash table. Must be a power of two, and must be less than 2^16 for
	// the original MoPaQ format, or less than 2^20 for the Burning Crusade format.
	private $hashTableSize;

	// Number of entries in the block table
	private $blockTableSize;

	//-- MPQ HEADER v 2 -------------------------------------------

	// Offset to the beginning of array of 16-bit high parts of file offsets.
	private $hiBlockTablePos64;

	// High 16 bits of the hash table offset for large archives.
	private $hashTablePosHi;

	// High 16 bits of the block table offset for large archives.
	private $blockTablePosHi;

	//-- MPQ HEADER v 3 -------------------------------------------

	// 64-bit version of the archive size
	private $archiveSize64;

	// 64-bit position of the BET table
	private $betTablePos64;

	// 64-bit position of the HET table
	private $hetTablePos64;

	//-- MPQ HEADER v 4 -------------------------------------------

	// Compressed size of the hash table
	private $hashTableSize64;

	// Compressed size of the block table
	private $blockTableSize64;

	// Compressed size of the hi-block table
	private $hiBlockTableSize64;

	// Compressed size of the HET block
	private $hetTableSize64;

	// Compressed size of the BET block
	private $betTableSize64;

	// Size of raw data chunk to calculate MD5.
	// MD5 of each data chunk follows the raw file data.
	private $rawChunkSize;

	// Array of MD5's
//unsigned char MD5_BlockTable[MD5_DIGEST_SIZE];      // MD5 of the block table before decryption
//unsigned char MD5_HashTable[MD5_DIGEST_SIZE];       // MD5 of the hash table before decryption
//unsigned char MD5_HiBlockTable[MD5_DIGEST_SIZE];    // MD5 of the hi-block table
//unsigned char MD5_BetTable[MD5_DIGEST_SIZE];        // MD5 of the BET table before decryption
//unsigned char MD5_HetTable[MD5_DIGEST_SIZE];        // MD5 of the HET table before decryption
//unsigned char MD5_MpqHeader[MD5_DIGEST_SIZE];       // MD5 of the MPQ header from signature to (including) MD5_HetTable

	// -----------------------------------------------------------------------------------------------------------------

	public static function parse(BinaryStreamParser $parser) {
		$header = new Header();

		$header->size = $parser->readUInt32();
		$header->archiveSize = $parser->readUInt32();
		$header->formatVersion = $parser->readUInt16();
		$header->blockSize = $parser->readUInt16();
		$header->hashTablePos = $parser->readUInt32();
		$header->blockTablePos = $parser->readUInt32();
		$header->hashTableSize = $parser->readUInt32();
		$header->blockTableSize = $parser->readUInt32();

		if($header->formatVersion >= self::ARCHIVE_FORMAT_2) {
			$parser->skip(8); //FIXME HiBlockTablePos64
            $header->hashTablePosHi = $parser->readUInt16();
            $header->blockTablePosHi = $parser->readUInt16();
		}

		// TODO implement other formats

		return $header;
	}

	/**
	 * @return integer
	 */
	public function getSize() {
		return $this->size;
	}

	/**
	 * @return integer
	 */
	public function getArchiveSize() {
		return $this->archiveSize;
	}

	/**
	 * @return integer
	 */
	public function getFormatVersion() {
		return $this->formatVersion;
	}

	/**
	 * @return mixed
	 */
	public function getBlockSize() {
		return $this->blockSize;
	}

	/**
	 * @return mixed
	 */
	public function getHashTablePos() {
		return $this->hashTablePos;
	}

	/**
	 * @return mixed
	 */
	public function getBlockTablePos() {
		return $this->blockTablePos;
	}

	/**
	 * @return mixed
	 */
	public function getHashTableSize() {
		return $this->hashTableSize;
	}

	/**
	 * @return mixed
	 */
	public function getBlockTableSize() {
		return $this->blockTableSize;
	}

	/**
	 * @return mixed
	 */
	public function getHiBlockTablePos64() {
		return $this->hiBlockTablePos64;
	}

	/**
	 * @return mixed
	 */
	public function getHashTablePosHi() {
		return $this->hashTablePosHi;
	}

	/**
	 * @return mixed
	 */
	public function getBlockTablePosHi() {
		return $this->blockTablePosHi;
	}

	/**
	 * @return mixed
	 */
	public function getArchiveSize64() {
		return $this->archiveSize64;
	}

	/**
	 * @return mixed
	 */
	public function getBetTablePos64() {
		return $this->betTablePos64;
	}

	/**
	 * @return mixed
	 */
	public function getHetTablePos64() {
		return $this->hetTablePos64;
	}

	/**
	 * @return mixed
	 */
	public function getHashTableSize64() {
		return $this->hashTableSize64;
	}

	/**
	 * @return mixed
	 */
	public function getBlockTableSize64() {
		return $this->blockTableSize64;
	}

	/**
	 * @return mixed
	 */
	public function getHiBlockTableSize64() {
		return $this->hiBlockTableSize64;
	}

	/**
	 * @return mixed
	 */
	public function getHetTableSize64() {
		return $this->hetTableSize64;
	}

	/**
	 * @return mixed
	 */
	public function getBetTableSize64() {
		return $this->betTableSize64;
	}

	/**
	 * @return mixed
	 */
	public function getRawChunkSize() {
		return $this->rawChunkSize;
	}

}