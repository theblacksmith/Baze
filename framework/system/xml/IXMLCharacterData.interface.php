<?php

interface IXMLCharacterData extends IXMLNode
{
	/**
	 * Gets the contents of the node.
	 * 
	 * @return string
	 */
	public function getdata();

	/**
	 * Sets the contents of the node.
	 * 
	 * @param string
	 */
	public function setdata($data);

	/**
	 * Returns the length of the contents.
	 * 
	 * @return int
	 */
	public function getlength();

	/**
	 * Append the string data to the end of the character data of the node.
	 * 
	 * @param string $data The string to append
	 */
	public function appendData($data);

	/* TODO: Require method
	 * Deletes <i>count</i> characters starting from position <i>offset</i> 
	 *
	 * @param int $offset The offset from which to start removing
	 * @param int $count The number of characters to delete. If the sum of offset  and count  exceeds the length, then all characters to the end of the data are deleted.
	 * 
	 * @throws DOM_INDEX_SIZE_ERR Raised if <i>offset</i> is negative or greater than the number of 16-bit units in data, or if <i>count</i> is negative.
	 *
	public function deleteData($offset, $count);
	 */

	/* TODO: Require method
	 * Inserts string <i>data</i> at position <i>offset</i>.
	 *
	 * @param int $offset
	 * @param string $data
	 * 
	 * @throws DOM_INDEX_SIZE_ERR Raised if <i>offset</i> is negative or greater than the number of 16-bit units in data.
	 *
	public function insertData($offset, $data);
	 */

	/* TODO: Require method
	 * Replace <i>count</i> characters starting from position <i>offset</i> with data.
	 * 
	 * @param int $offset The offset from which to start replacing.
	 * @param int $count The number of characters to replace. If the sum of offset  and count  exceeds the length, then all characters to the end of the data are replaced.
	 * @param string $data The string with which the range must be replaced.
	 *
	public function replaceData(int $offset, int $count, string $data);
	 */

	/* TODO: Require method
	 * Returns the specified substring.
	 * 
	 * @param int $offset Start offset of substring to extract.
	 * @param int $count The number of characters to extract.
	 *
	public function substringData(int $offset, int $count);
	 */
}
