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

class Sector {

	/**
	 * The sector index
	 *
	 * @var integer
	 */
	private $index;

	/**
	 * The sector starting position (relative to the parent block)
	 *
	 * @var integer
	 */
	private $start;

	/**
	 * The sector ending position (relative to the parent block)
	 *
	 * @var integer
	 */
	private $end;

	// -----------------------------------------------------------------------------------------------------------------

	/**
	 * Sector constructor.
	 * @param $index integer the sector index
	 * @param $start integer the sector starting position (relative to the parent block)
	 * @param $end integer the sector ending position (relative to the parent block)
	 */
	public function __construct($index, $start, $end) {
		$this->index = $index;
		$this->start = $start;
		$this->end = $end;
	}

	// -----------------------------------------------------------------------------------------------------------------

	/**
	 * @return mixed
	 */
	public function getIndex() {
		return $this->index;
	}

	/**
	 * @return mixed
	 */
	public function getStart() {
		return $this->start;
	}

	/**
	 * @return mixed
	 */
	public function getEnd() {
		return $this->end;
	}

	public function getLength() {
		return $this->end - $this->start;
	}

	// -----------------------------------------------------------------------------------------------------------------

	public function intersectionBegin($start, $length) {
		if($start < $this->start) {
			return $this->start;
		}
		return $start - $this->start;
	}

	public function intersectionEnd($start, $length) {
		if(($start + $length) > $this->end) {
			return $this->getLength();
		}
		return $length;
	}

	public function contains($start, $length) {
		if($start >= $this->start) {
			if($start <= ($this->end)) {
				return true;
			}
		}
		return false;
	}

	public function fullyContains($start, $length) {
		if($start >= $this->start) {
			if(($start + $length) <= ($this->end)) {
				return true;
			}
		}
		return false;
	}

}