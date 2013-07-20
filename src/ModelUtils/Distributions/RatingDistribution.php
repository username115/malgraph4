<?php
class RatingDistribution extends AbstractDistribution
{
	protected function __construct()
	{
		foreach (range(10, 0) as $x)
		{
			$this->addGroup($x);
		}
	}

	public function getNullGroupKey()
	{
		return 0;
	}

	public function addEntry($entry)
	{
		$this->addToGroup($entry->score, $entry);
	}

	protected function sortGroups()
	{
		krsort($this->groups, SORT_NUMERIC);
		krsort($this->entries, SORT_NUMERIC);
	}

	public function getRatedCount()
	{
		return $this->getTotalSize(self::IGNORE_NULL_KEY);
	}

	public function getUnratedCount()
	{
		return $this->getGroupSize($this->getNullGroupKey());
	}

	public function getMeanScore()
	{
		$mean = 0;
		$totalSize = 0;
		foreach ($this->groups as $key => $size)
		{
			if ($key == $this->getNullGroupKey())
			{
				continue;
			}
			$mean += $key * $size;
			$totalSize += $size;
		}
		if ($totalSize == 0)
		{
			return null;
		}
		return $mean / max(1, $totalSize);
	}

	public function getStandardDeviation()
	{
		$standardDeviation = 0;
		$meanScore = $this->getMeanScore();
		foreach ($this->groups as $score => $size)
		{
			if ($score != $this->getNullGroupKey())
			{
				$standardDeviation += $size * pow($score - $meanScore, 2);
			}
		}
		$standardDeviation /= max(1, $this->getRatedCount() - 1);
		$standardDeviation = sqrt($standardDeviation);
		return $standardDeviation;
	}
}
