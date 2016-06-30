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

namespace Rogiel\MPQ\Stream;

use Rogiel\MPQ\Compression\Compression;

class CompressedStream implements Stream{

	/**
	 * @var Stream
	 */
	private $stream;

	/**
	 * @var Compression
	 */
	private $compression;

	// -----------------------------------------------------------------------------------------------------------------

	public function __construct(Stream $stream, Compression $compression) {
		$this->stream = $stream;
		$this->compression = $compression;
	}

	// -----------------------------------------------------------------------------------------------------------------

	/**
	 * {@inheritdoc}
	 */
	public function close() {
		$this->stream->close();
	}

	/**
	 * {@inheritdoc}
	 */
	public function readByte() {
		return $this->readBytes(1);
	}

	/**
	 * {@inheritdoc}
	 */
	public function readBytes($bytes) {
		return $this->compression->decompress($this->stream->readBytes($bytes), $bytes);
	}

	/**
	 * {@inheritdoc}
	 */
	public function seek($position) {
		$this->stream->seek($position);
	}

	/**
	 * {@inheritdoc}
	 */
	public function skip($position) {
		$this->stream->skip($position);
	}

	/**
	 * {@inheritdoc}
	 */
	public function eof() {
		return $this->stream->eof();
	}

}