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

namespace Rogiel\MPQ\Stream\Block;


use Rogiel\MPQ\Compression\BZIPCompression;
use Rogiel\MPQ\Compression\DeflateCompression;
use Rogiel\MPQ\Exception\Compression\CompressionException;
use Rogiel\MPQ\Metadata\Block;
use Rogiel\MPQ\MPQFile;
use Rogiel\MPQ\Stream\CompressedStream;
use Rogiel\MPQ\Stream\Parser\BinaryStreamParser;
use Rogiel\MPQ\Stream\Stream;

class BlockStream implements Stream {

	/**
	 * @var MPQFile
	 */
	private $file;

	/**
	 * @var Stream
	 */
	private $stream;

	/**
	 * @var Block
	 */
	private $block;

	/**
	 * @var array
	 */
	private $sectors;

	// -----------------------------------------------------------------------------------------------------------------

	private $position;
	private $buffer;

	private $positionInSector;

	/**
	 * @var Sector
	 */
	private $currentSector;

	// -----------------------------------------------------------------------------------------------------------------

	/**
	 * BlockStream constructor.
	 * @param MPQFile $file
	 * @param Stream $stream
	 * @param Block $block
	 * @param array $sectors
	 */
	public function __construct(MPQFile $file, Stream $stream, Block $block, array $sectors) {
		$this->file = $file;
		$this->stream = $stream;
		$this->block = $block;

		$this->sectors = array();
		$c = count($sectors) - 1;
		for ($i = 0; $i < $c; $i++) {
			$this->sectors[] = new Sector($i, $sectors[$i], $sectors[$i + 1]);
		}

		$this->position = 0;
		$this->buffer = NULL;
		$this->currentSector = $this->sectors[0];
		$this->positionInSector = 0;
	}

	// -----------------------------------------------------------------------------------------------------------------

	public function close() {
		$this->stream->close();
	}

	public function readByte() {
		return $this->readBytes(1);
	}

	public function readBytes($bytes) {
		if($this->eof()) {
			return false;
		}
		if(($this->position + $bytes) > $this->block->getSize()) {
			$bytes = $this->block->getSize() - $this->position;
		}

		if($this->buffer === NULL) {
			$this->buffer = $this->readSector($this->currentSector);
			$this->positionInSector = 0;
		} else if($this->positionInSector >= strlen($this->buffer)) {
			$this->currentSector = $this->sectors[$this->currentSector->getIndex() + 1];
			$this->buffer = $this->readSector($this->currentSector);
			$this->positionInSector = 0;
		}

		$data = substr(
			$this->buffer,
			$this->positionInSector,
			$bytes
		);
		$this->position += strlen($data);
		$this->positionInSector += strlen($data);

		return $data;
	}

	public function seek($position) {
		if($this->block->isCompressed()) {
			throw new \RuntimeException("Seek is not supported on compressed streams");
		}
		$this->position = $position;
	}

	public function skip($bytes) {
		if($this->block->isCompressed()) {
			throw new \RuntimeException("Seek is not supported on compressed streams");
		}
		$this->position += $bytes;
	}

	public function eof() {
		return $this->position >= $this->block->getSize();
	}

	// -----------------------------------------------------------------------------------------------------------------

	private function readSector(Sector $sector) {
		$this->stream->seek($this->file->getUserData()->getHeaderOffset()
			+ $this->block->getFilePos()
			+ $sector->getStart());

		$compressedStream = $this->createCompressedStream();
		return $compressedStream->readBytes($sector->getLength());
	}

	private function createCompressedStream() {
		$stream = $this->stream;

		if($this->block->isCompressed() && $this->block->getSize() > $this->block->getCompressedSize()) {
			$parser = new BinaryStreamParser($this->stream);
			$compressionType = $parser->readByte();
			switch ($compressionType) {
				case 0x00: return $stream;
				case 0x02: return new CompressedStream($stream, new DeflateCompression());
				case 0x10: return new CompressedStream($stream, new BZIPCompression());
				default:
					throw new CompressionException(sprintf('Invalid compression format: %s', $compressionType));
			}
		}
		return $stream;
	}

}