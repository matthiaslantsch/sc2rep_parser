<?php

return array(
	0 => new BinInteger(7),
	1 => new BinInteger(4),
	2 => new BinInteger(6),
	3 => new BinInteger(14),
	4 => new BinInteger(22),
	5 => new BinInteger(32),
	6 => new BinChoice(new BinInteger(2), [
		0 => new BinInteger(6),
		1 => new BinInteger(14),
		2 => new BinInteger(22),
		3 => new BinInteger(32),
	]),
	7 => new BinBlob(new BinInteger(8)),
	8 => new BinInteger(8),
	9 => new BinStruct([
		"flags" => new BinInteger(8),
		"major" => new BinInteger(8),
		"minor" => new BinInteger(8),
		"revision" => new BinInteger(8),
		"build" => new BinInteger(32),
		"baseBuild" => new BinInteger(32),
	]),
	10 => new BinInteger(3),
	11 => new BinStruct([
		"signature" => new BinBlob(new BinInteger(8)),
		"version" => new BinStruct([
			"flags" => new BinInteger(8),
			"major" => new BinInteger(8),
			"minor" => new BinInteger(8),
			"revision" => new BinInteger(8),
			"build" => new BinInteger(32),
			"baseBuild" => new BinInteger(32),
		]),
		"type" => new BinInteger(3),
		"elapsedGameLoops" => new BinInteger(32),
	]),
	12 => new BinBlob(4),
	13 => new BinBlob(new BinInteger(7)),
	14 => new BinStruct([
		"region" => new BinInteger(8),
		"programId" => new BinBlob(4),
		"realm" => new BinInteger(32),
		"name" => new BinBlob(new BinInteger(7)),
	]),
	15 => new BinStruct([
		"a" => new BinInteger(8),
		"r" => new BinInteger(8),
		"g" => new BinInteger(8),
		"b" => new BinInteger(8),
	]),
	16 => new BinInteger(2),
	17 => new BinStruct([
		"name" => new BinBlob(new BinInteger(8)),
		"toon" => new BinStruct([
			"region" => new BinInteger(8),
			"programId" => new BinBlob(4),
			"realm" => new BinInteger(32),
			"name" => new BinBlob(new BinInteger(7)),
		]),
		"race" => new BinBlob(new BinInteger(8)),
		"color" => new BinStruct([
			"a" => new BinInteger(8),
			"r" => new BinInteger(8),
			"g" => new BinInteger(8),
			"b" => new BinInteger(8),
		]),
		"control" => new BinInteger(8),
		"teamId" => new BinInteger(4),
		"handicap" => new BinInteger(7),
		"observe" => new BinInteger(2),
		"result" => new BinInteger(2),
	]),
	18 => new BinArray(
		new BinInteger(5),
		new BinStruct([
			"name" => new BinBlob(new BinInteger(8)),
			"toon" => new BinStruct([
				"region" => new BinInteger(8),
				"programId" => new BinBlob(4),
				"realm" => new BinInteger(32),
				"name" => new BinBlob(new BinInteger(7)),
			]),
			"race" => new BinBlob(new BinInteger(8)),
			"color" => new BinStruct([
				"a" => new BinInteger(8),
				"r" => new BinInteger(8),
				"g" => new BinInteger(8),
				"b" => new BinInteger(8),
			]),
			"control" => new BinInteger(8),
			"teamId" => new BinInteger(4),
			"handicap" => new BinInteger(7),
			"observe" => new BinInteger(2),
			"result" => new BinInteger(2),
		])
	),
	19 => new BinOptional(new BinArray(
		new BinInteger(5),
		new BinStruct([
			"name" => new BinBlob(new BinInteger(8)),
			"toon" => new BinStruct([
				"region" => new BinInteger(8),
				"programId" => new BinBlob(4),
				"realm" => new BinInteger(32),
				"name" => new BinBlob(new BinInteger(7)),
			]),
			"race" => new BinBlob(new BinInteger(8)),
			"color" => new BinStruct([
				"a" => new BinInteger(8),
				"r" => new BinInteger(8),
				"g" => new BinInteger(8),
				"b" => new BinInteger(8),
			]),
			"control" => new BinInteger(8),
			"teamId" => new BinInteger(4),
			"handicap" => new BinInteger(7),
			"observe" => new BinInteger(2),
			"result" => new BinInteger(2),
		])
	), new BinInteger(1)),
	20 => new BinBlob(new BinInteger(10)),
	21 => new BinBlob(new BinInteger(11)),
	22 => new BinStruct([
		"file" => new BinBlob(new BinInteger(11)),
	]),
	23 => new BinInteger(1),
	24 => new BinInteger(64, -9223372036854775808),
	25 => new BinBlob(new BinInteger(12)),
	26 => new BinBlob(new BinInteger(0, 40)),
	27 => new BinArray(
		new BinInteger(4),
		new BinBlob(new BinInteger(0, 40))
	),
	28 => new BinOptional(new BinArray(
		new BinInteger(4),
		new BinBlob(new BinInteger(0, 40))
	), new BinInteger(1)),
	29 => new BinStruct([
		"playerList" => new BinOptional(new BinArray(
			new BinInteger(5),
			new BinStruct([
				"name" => new BinBlob(new BinInteger(8)),
				"toon" => new BinStruct([
					"region" => new BinInteger(8),
					"programId" => new BinBlob(4),
					"realm" => new BinInteger(32),
					"name" => new BinBlob(new BinInteger(7)),
				]),
				"race" => new BinBlob(new BinInteger(8)),
				"color" => new BinStruct([
					"a" => new BinInteger(8),
					"r" => new BinInteger(8),
					"g" => new BinInteger(8),
					"b" => new BinInteger(8),
				]),
				"control" => new BinInteger(8),
				"teamId" => new BinInteger(4),
				"handicap" => new BinInteger(7),
				"observe" => new BinInteger(2),
				"result" => new BinInteger(2),
			])
		), new BinInteger(1)),
		"title" => new BinBlob(new BinInteger(10)),
		"difficulty" => new BinBlob(new BinInteger(8)),
		"thumbnail" => new BinStruct([
			"file" => new BinBlob(new BinInteger(11)),
		]),
		"isBlizzardMap" => new BinInteger(1),
		"timeUTC" => new BinInteger(64, -9223372036854775808),
		"timeLocalOffset" => new BinInteger(64, -9223372036854775808),
		"description" => new BinBlob(new BinInteger(12)),
		"imageFilePath" => new BinBlob(new BinInteger(10)),
		"mapFileName" => new BinBlob(new BinInteger(10)),
		"cacheHandles" => new BinOptional(new BinArray(
			new BinInteger(4),
			new BinBlob(new BinInteger(0, 40))
		), new BinInteger(1)),
		"miniSave" => new BinInteger(1),
		"gameSpeed" => new BinInteger(3),
		"defaultDifficulty" => new BinInteger(6),
	]),
	30 => new BinOptional(new BinInteger(8), new BinInteger(1)),
	31 => new BinStruct([
		"race" => new BinOptional(new BinInteger(8), new BinInteger(1)),
	]),
	32 => new BinStruct([
		"name" => new BinBlob(new BinInteger(8)),
		"randomSeed" => new BinInteger(32),
		"racePreference" => new BinStruct([
			"race" => new BinOptional(new BinInteger(8), new BinInteger(1)),
		]),
		"testMap" => new BinInteger(1),
		"testAuto" => new BinInteger(1),
		"observe" => new BinInteger(2),
	]),
	33 => new BinArray(
		new BinInteger(5),
		new BinStruct([
			"name" => new BinBlob(new BinInteger(8)),
			"randomSeed" => new BinInteger(32),
			"racePreference" => new BinStruct([
				"race" => new BinOptional(new BinInteger(8), new BinInteger(1)),
			]),
			"testMap" => new BinInteger(1),
			"testAuto" => new BinInteger(1),
			"observe" => new BinInteger(2),
		])
	),
	34 => new BinStruct([
		"lockTeams" => new BinInteger(1),
		"teamsTogether" => new BinInteger(1),
		"advancedSharedControl" => new BinInteger(1),
		"randomRaces" => new BinInteger(1),
		"battleNet" => new BinInteger(1),
		"amm" => new BinInteger(1),
		"ranked" => new BinInteger(1),
		"noVictoryOrDefeat" => new BinInteger(1),
		"fog" => new BinInteger(2),
		"observers" => new BinInteger(2),
		"userDifficulty" => new BinInteger(2),
	]),
	35 => new BinInteger(5),
	36 => new BinInteger(4, 1),
	37 => new BinInteger(5, 1),
	38 => new BinInteger(8, 1),
	39 => new BinInteger(new BinInteger(6)),
	40 => new BinInteger(new BinInteger(8)),
	41 => new BinInteger(new BinInteger(2)),
	42 => new BinStruct([
		"allowedColors" => new BinInteger(new BinInteger(6)),
		"allowedRaces" => new BinInteger(new BinInteger(8)),
		"allowedDifficulty" => new BinInteger(new BinInteger(6)),
		"allowedControls" => new BinInteger(new BinInteger(8)),
		"allowedObserveTypes" => new BinInteger(new BinInteger(2)),
	]),
	43 => new BinArray(
		new BinInteger(5),
		new BinStruct([
			"allowedColors" => new BinInteger(new BinInteger(6)),
			"allowedRaces" => new BinInteger(new BinInteger(8)),
			"allowedDifficulty" => new BinInteger(new BinInteger(6)),
			"allowedControls" => new BinInteger(new BinInteger(8)),
			"allowedObserveTypes" => new BinInteger(new BinInteger(2)),
		])
	),
	44 => new BinStruct([
		"randomValue" => new BinInteger(32),
		"gameCacheName" => new BinBlob(new BinInteger(10)),
		"gameOptions" => new BinStruct([
			"lockTeams" => new BinInteger(1),
			"teamsTogether" => new BinInteger(1),
			"advancedSharedControl" => new BinInteger(1),
			"randomRaces" => new BinInteger(1),
			"battleNet" => new BinInteger(1),
			"amm" => new BinInteger(1),
			"ranked" => new BinInteger(1),
			"noVictoryOrDefeat" => new BinInteger(1),
			"fog" => new BinInteger(2),
			"observers" => new BinInteger(2),
			"userDifficulty" => new BinInteger(2),
		]),
		"gameSpeed" => new BinInteger(3),
		"gameType" => new BinInteger(3),
		"maxUsers" => new BinInteger(5),
		"maxObservers" => new BinInteger(5),
		"maxPlayers" => new BinInteger(4),
		"maxTeams" => new BinInteger(4, 1),
		"maxColors" => new BinInteger(5, 1),
		"maxRaces" => new BinInteger(8, 1),
		"maxControls" => new BinInteger(8, 1),
		"mapSizeX" => new BinInteger(8),
		"mapSizeY" => new BinInteger(8),
		"mapFileSyncChecksum" => new BinInteger(32),
		"mapFileName" => new BinBlob(new BinInteger(10)),
		"modFileSyncChecksum" => new BinInteger(32),
		"slotDescriptions" => new BinArray(
			new BinInteger(5),
			new BinStruct([
				"allowedColors" => new BinInteger(new BinInteger(6)),
				"allowedRaces" => new BinInteger(new BinInteger(8)),
				"allowedDifficulty" => new BinInteger(new BinInteger(6)),
				"allowedControls" => new BinInteger(new BinInteger(8)),
				"allowedObserveTypes" => new BinInteger(new BinInteger(2)),
			])
		),
		"defaultDifficulty" => new BinInteger(6),
		"cacheHandles" => new BinArray(
			new BinInteger(4),
			new BinBlob(new BinInteger(0, 40))
		),
		"isBlizzardMap" => new BinInteger(1),
	]),
	45 => new BinOptional(new BinInteger(4), new BinInteger(1)),
	46 => new BinOptional(new BinInteger(5), new BinInteger(1)),
	47 => new BinStruct([
		"color" => new BinOptional(new BinInteger(5), new BinInteger(1)),
	]),
	48 => new BinInteger(16),
	49 => new BinArray(
		new BinInteger(5),
		new BinInteger(16)
	),
	50 => new BinStruct([
		"control" => new BinInteger(8),
		"userId" => new BinOptional(new BinInteger(4), new BinInteger(1)),
		"teamId" => new BinInteger(4),
		"colorPref" => new BinStruct([
			"color" => new BinOptional(new BinInteger(5), new BinInteger(1)),
		]),
		"racePref" => new BinStruct([
			"race" => new BinOptional(new BinInteger(8), new BinInteger(1)),
		]),
		"difficulty" => new BinInteger(6),
		"handicap" => new BinInteger(7),
		"observe" => new BinInteger(2),
		"rewards" => new BinArray(
			new BinInteger(5),
			new BinInteger(16)
		),
	]),
	51 => new BinArray(
		new BinInteger(5),
		new BinStruct([
			"control" => new BinInteger(8),
			"userId" => new BinOptional(new BinInteger(4), new BinInteger(1)),
			"teamId" => new BinInteger(4),
			"colorPref" => new BinStruct([
				"color" => new BinOptional(new BinInteger(5), new BinInteger(1)),
			]),
			"racePref" => new BinStruct([
				"race" => new BinOptional(new BinInteger(8), new BinInteger(1)),
			]),
			"difficulty" => new BinInteger(6),
			"handicap" => new BinInteger(7),
			"observe" => new BinInteger(2),
			"rewards" => new BinArray(
				new BinInteger(5),
				new BinInteger(16)
			),
		])
	),
	52 => new BinStruct([
		"phase" => new BinInteger(3),
		"maxUsers" => new BinInteger(5),
		"maxObservers" => new BinInteger(5),
		"slots" => new BinArray(
			new BinInteger(5),
			new BinStruct([
				"control" => new BinInteger(8),
				"userId" => new BinOptional(new BinInteger(4), new BinInteger(1)),
				"teamId" => new BinInteger(4),
				"colorPref" => new BinStruct([
					"color" => new BinOptional(new BinInteger(5), new BinInteger(1)),
				]),
				"racePref" => new BinStruct([
					"race" => new BinOptional(new BinInteger(8), new BinInteger(1)),
				]),
				"difficulty" => new BinInteger(6),
				"handicap" => new BinInteger(7),
				"observe" => new BinInteger(2),
				"rewards" => new BinArray(
					new BinInteger(5),
					new BinInteger(16)
				),
			])
		),
		"randomSeed" => new BinInteger(32),
		"hostUserId" => new BinOptional(new BinInteger(4), new BinInteger(1)),
		"isSinglePlayer" => new BinInteger(1),
		"gameDuration" => new BinInteger(32),
		"defaultDifficulty" => new BinInteger(6),
	]),
	53 => new BinStruct([
		"userInitialData" => new BinArray(
			new BinInteger(5),
			new BinStruct([
				"name" => new BinBlob(new BinInteger(8)),
				"randomSeed" => new BinInteger(32),
				"racePreference" => new BinStruct([
					"race" => new BinOptional(new BinInteger(8), new BinInteger(1)),
				]),
				"testMap" => new BinInteger(1),
				"testAuto" => new BinInteger(1),
				"observe" => new BinInteger(2),
			])
		),
		"gameDescription" => new BinStruct([
			"randomValue" => new BinInteger(32),
			"gameCacheName" => new BinBlob(new BinInteger(10)),
			"gameOptions" => new BinStruct([
				"lockTeams" => new BinInteger(1),
				"teamsTogether" => new BinInteger(1),
				"advancedSharedControl" => new BinInteger(1),
				"randomRaces" => new BinInteger(1),
				"battleNet" => new BinInteger(1),
				"amm" => new BinInteger(1),
				"ranked" => new BinInteger(1),
				"noVictoryOrDefeat" => new BinInteger(1),
				"fog" => new BinInteger(2),
				"observers" => new BinInteger(2),
				"userDifficulty" => new BinInteger(2),
			]),
			"gameSpeed" => new BinInteger(3),
			"gameType" => new BinInteger(3),
			"maxUsers" => new BinInteger(5),
			"maxObservers" => new BinInteger(5),
			"maxPlayers" => new BinInteger(4),
			"maxTeams" => new BinInteger(4, 1),
			"maxColors" => new BinInteger(5, 1),
			"maxRaces" => new BinInteger(8, 1),
			"maxControls" => new BinInteger(8, 1),
			"mapSizeX" => new BinInteger(8),
			"mapSizeY" => new BinInteger(8),
			"mapFileSyncChecksum" => new BinInteger(32),
			"mapFileName" => new BinBlob(new BinInteger(10)),
			"modFileSyncChecksum" => new BinInteger(32),
			"slotDescriptions" => new BinArray(
				new BinInteger(5),
				new BinStruct([
					"allowedColors" => new BinInteger(new BinInteger(6)),
					"allowedRaces" => new BinInteger(new BinInteger(8)),
					"allowedDifficulty" => new BinInteger(new BinInteger(6)),
					"allowedControls" => new BinInteger(new BinInteger(8)),
					"allowedObserveTypes" => new BinInteger(new BinInteger(2)),
				])
			),
			"defaultDifficulty" => new BinInteger(6),
			"cacheHandles" => new BinArray(
				new BinInteger(4),
				new BinBlob(new BinInteger(0, 40))
			),
			"isBlizzardMap" => new BinInteger(1),
		]),
		"lobbyState" => new BinStruct([
			"phase" => new BinInteger(3),
			"maxUsers" => new BinInteger(5),
			"maxObservers" => new BinInteger(5),
			"slots" => new BinArray(
				new BinInteger(5),
				new BinStruct([
					"control" => new BinInteger(8),
					"userId" => new BinOptional(new BinInteger(4), new BinInteger(1)),
					"teamId" => new BinInteger(4),
					"colorPref" => new BinStruct([
						"color" => new BinOptional(new BinInteger(5), new BinInteger(1)),
					]),
					"racePref" => new BinStruct([
						"race" => new BinOptional(new BinInteger(8), new BinInteger(1)),
					]),
					"difficulty" => new BinInteger(6),
					"handicap" => new BinInteger(7),
					"observe" => new BinInteger(2),
					"rewards" => new BinArray(
						new BinInteger(5),
						new BinInteger(16)
					),
				])
			),
			"randomSeed" => new BinInteger(32),
			"hostUserId" => new BinOptional(new BinInteger(4), new BinInteger(1)),
			"isSinglePlayer" => new BinInteger(1),
			"gameDuration" => new BinInteger(32),
			"defaultDifficulty" => new BinInteger(6),
		]),
	]),
	54 => new BinStruct([
		"syncLobbyState" => new BinStruct([
			"userInitialData" => new BinArray(
				new BinInteger(5),
				new BinStruct([
					"name" => new BinBlob(new BinInteger(8)),
					"randomSeed" => new BinInteger(32),
					"racePreference" => new BinStruct([
						"race" => new BinOptional(new BinInteger(8), new BinInteger(1)),
					]),
					"testMap" => new BinInteger(1),
					"testAuto" => new BinInteger(1),
					"observe" => new BinInteger(2),
				])
			),
			"gameDescription" => new BinStruct([
				"randomValue" => new BinInteger(32),
				"gameCacheName" => new BinBlob(new BinInteger(10)),
				"gameOptions" => new BinStruct([
					"lockTeams" => new BinInteger(1),
					"teamsTogether" => new BinInteger(1),
					"advancedSharedControl" => new BinInteger(1),
					"randomRaces" => new BinInteger(1),
					"battleNet" => new BinInteger(1),
					"amm" => new BinInteger(1),
					"ranked" => new BinInteger(1),
					"noVictoryOrDefeat" => new BinInteger(1),
					"fog" => new BinInteger(2),
					"observers" => new BinInteger(2),
					"userDifficulty" => new BinInteger(2),
				]),
				"gameSpeed" => new BinInteger(3),
				"gameType" => new BinInteger(3),
				"maxUsers" => new BinInteger(5),
				"maxObservers" => new BinInteger(5),
				"maxPlayers" => new BinInteger(4),
				"maxTeams" => new BinInteger(4, 1),
				"maxColors" => new BinInteger(5, 1),
				"maxRaces" => new BinInteger(8, 1),
				"maxControls" => new BinInteger(8, 1),
				"mapSizeX" => new BinInteger(8),
				"mapSizeY" => new BinInteger(8),
				"mapFileSyncChecksum" => new BinInteger(32),
				"mapFileName" => new BinBlob(new BinInteger(10)),
				"modFileSyncChecksum" => new BinInteger(32),
				"slotDescriptions" => new BinArray(
					new BinInteger(5),
					new BinStruct([
						"allowedColors" => new BinInteger(new BinInteger(6)),
						"allowedRaces" => new BinInteger(new BinInteger(8)),
						"allowedDifficulty" => new BinInteger(new BinInteger(6)),
						"allowedControls" => new BinInteger(new BinInteger(8)),
						"allowedObserveTypes" => new BinInteger(new BinInteger(2)),
					])
				),
				"defaultDifficulty" => new BinInteger(6),
				"cacheHandles" => new BinArray(
					new BinInteger(4),
					new BinBlob(new BinInteger(0, 40))
				),
				"isBlizzardMap" => new BinInteger(1),
			]),
			"lobbyState" => new BinStruct([
				"phase" => new BinInteger(3),
				"maxUsers" => new BinInteger(5),
				"maxObservers" => new BinInteger(5),
				"slots" => new BinArray(
					new BinInteger(5),
					new BinStruct([
						"control" => new BinInteger(8),
						"userId" => new BinOptional(new BinInteger(4), new BinInteger(1)),
						"teamId" => new BinInteger(4),
						"colorPref" => new BinStruct([
							"color" => new BinOptional(new BinInteger(5), new BinInteger(1)),
						]),
						"racePref" => new BinStruct([
							"race" => new BinOptional(new BinInteger(8), new BinInteger(1)),
						]),
						"difficulty" => new BinInteger(6),
						"handicap" => new BinInteger(7),
						"observe" => new BinInteger(2),
						"rewards" => new BinArray(
							new BinInteger(5),
							new BinInteger(16)
						),
					])
				),
				"randomSeed" => new BinInteger(32),
				"hostUserId" => new BinOptional(new BinInteger(4), new BinInteger(1)),
				"isSinglePlayer" => new BinInteger(1),
				"gameDuration" => new BinInteger(32),
				"defaultDifficulty" => new BinInteger(6),
			]),
		]),
	]),
	55 => new BinStruct([
		"name" => new BinBlob(new BinInteger(7)),
	]),
	56 => new BinBlob(new BinInteger(6)),
	57 => new BinStruct([
		"name" => new BinBlob(new BinInteger(6)),
	]),
	58 => new BinStruct([
		"name" => new BinBlob(new BinInteger(6)),
		"type" => new BinInteger(32),
		"data" => new BinBlob(new BinInteger(7)),
	]),
	59 => new BinStruct([
		"type" => new BinInteger(32),
		"name" => new BinBlob(new BinInteger(6)),
		"data" => new BinBlob(new BinInteger(12)),
	]),
	60 => new BinStruct([
		"developmentCheatsEnabled" => new BinInteger(1),
		"multiplayerCheatsEnabled" => new BinInteger(1),
		"syncChecksummingEnabled" => new BinInteger(1),
		"isMapToMapTransition" => new BinInteger(1),
	]),
	61 => new BinStruct([
	]),
	62 => new BinStruct([
		"fileName" => new BinBlob(new BinInteger(10)),
		"automatic" => new BinInteger(1),
		"overwrite" => new BinInteger(1),
		"name" => new BinBlob(new BinInteger(8)),
		"description" => new BinBlob(new BinInteger(10)),
	]),
	63 => new BinInteger(32, -2147483648),
	64 => new BinStruct([
		"x" => new BinInteger(32, -2147483648),
		"y" => new BinInteger(32, -2147483648),
	]),
	65 => new BinStruct([
		"point" => new BinStruct([
			"x" => new BinInteger(32, -2147483648),
			"y" => new BinInteger(32, -2147483648),
		]),
		"time" => new BinInteger(32, -2147483648),
		"verb" => new BinBlob(new BinInteger(10)),
		"arguments" => new BinBlob(new BinInteger(10)),
	]),
	66 => new BinStruct([
		"data" => new BinStruct([
			"point" => new BinStruct([
				"x" => new BinInteger(32, -2147483648),
				"y" => new BinInteger(32, -2147483648),
			]),
			"time" => new BinInteger(32, -2147483648),
			"verb" => new BinBlob(new BinInteger(10)),
			"arguments" => new BinBlob(new BinInteger(10)),
		]),
	]),
	67 => new BinStruct([
		"x" => new BinInteger(32, -2147483648),
		"y" => new BinInteger(32, -2147483648),
		"z" => new BinInteger(32, -2147483648),
	]),
	68 => new BinStruct([
		"cmdFlags" => new BinInteger(32),
		"abilLink" => new BinInteger(16),
		"abilCmdIndex" => new BinInteger(8),
		"abilCmdData" => new BinInteger(8),
		"targetUnitFlags" => new BinInteger(8),
		"targetUnitTimer" => new BinInteger(8),
		"otherUnit" => new BinInteger(32),
		"targetUnitTag" => new BinInteger(32),
		"targetUnitSnapshotUnitLink" => new BinInteger(16),
		"targetUnitSnapshotPlayerId" => new BinOptional(new BinInteger(4), new BinInteger(1)),
		"targetPoint" => new BinStruct([
			"x" => new BinInteger(32, -2147483648),
			"y" => new BinInteger(32, -2147483648),
			"z" => new BinInteger(32, -2147483648),
		]),
	]),
	69 => new BinStruct([
		"__parent" => new BinInteger(new BinInteger(8)),
	]),
	70 => new BinStruct([
		"unitLink" => new BinInteger(16),
		"intraSubgroupPriority" => new BinInteger(8),
		"count" => new BinInteger(8),
	]),
	71 => new BinArray(
		new BinInteger(8),
		new BinStruct([
			"unitLink" => new BinInteger(16),
			"intraSubgroupPriority" => new BinInteger(8),
			"count" => new BinInteger(8),
		])
	),
	72 => new BinArray(
		new BinInteger(8),
		new BinInteger(32)
	),
	73 => new BinStruct([
		"subgroupIndex" => new BinInteger(8),
		"removeMask" => new BinStruct([
			"__parent" => new BinInteger(new BinInteger(8)),
		]),
		"addSubgroups" => new BinArray(
			new BinInteger(8),
			new BinStruct([
				"unitLink" => new BinInteger(16),
				"intraSubgroupPriority" => new BinInteger(8),
				"count" => new BinInteger(8),
			])
		),
		"addUnitTags" => new BinArray(
			new BinInteger(8),
			new BinInteger(32)
		),
	]),
	74 => new BinStruct([
		"controlGroupId" => new BinInteger(4),
		"delta" => new BinStruct([
			"subgroupIndex" => new BinInteger(8),
			"removeMask" => new BinStruct([
				"__parent" => new BinInteger(new BinInteger(8)),
			]),
			"addSubgroups" => new BinArray(
				new BinInteger(8),
				new BinStruct([
					"unitLink" => new BinInteger(16),
					"intraSubgroupPriority" => new BinInteger(8),
					"count" => new BinInteger(8),
				])
			),
			"addUnitTags" => new BinArray(
				new BinInteger(8),
				new BinInteger(32)
			),
		]),
	]),
	75 => new BinStruct([
		"controlGroupIndex" => new BinInteger(4),
		"controlGroupUpdate" => new BinInteger(2),
		"mask" => new BinStruct([
			"__parent" => new BinInteger(new BinInteger(8)),
		]),
	]),
	76 => new BinStruct([
		"count" => new BinInteger(8),
		"subgroupCount" => new BinInteger(8),
		"activeSubgroupIndex" => new BinInteger(8),
		"unitTagsChecksum" => new BinInteger(32),
		"subgroupIndicesChecksum" => new BinInteger(32),
		"subgroupsChecksum" => new BinInteger(32),
	]),
	77 => new BinStruct([
		"controlGroupId" => new BinInteger(4),
		"selectionSyncData" => new BinStruct([
			"count" => new BinInteger(8),
			"subgroupCount" => new BinInteger(8),
			"activeSubgroupIndex" => new BinInteger(8),
			"unitTagsChecksum" => new BinInteger(32),
			"subgroupIndicesChecksum" => new BinInteger(32),
			"subgroupsChecksum" => new BinInteger(32),
		]),
	]),
	78 => new BinArray(
		new BinInteger(3),
		new BinInteger(32, -2147483648)
	),
	79 => new BinStruct([
		"recipientId" => new BinInteger(4),
		"resources" => new BinArray(
			new BinInteger(3),
			new BinInteger(32, -2147483648)
		),
	]),
	80 => new BinStruct([
		"chatMessage" => new BinBlob(new BinInteger(10)),
	]),
	81 => new BinInteger(8, -128),
	82 => new BinStruct([
		"beacon" => new BinInteger(8, -128),
		"ally" => new BinInteger(8, -128),
		"autocast" => new BinInteger(8, -128),
		"targetUnitTag" => new BinInteger(32),
		"targetUnitSnapshotUnitLink" => new BinInteger(16),
		"targetUnitSnapshotPlayerId" => new BinOptional(new BinInteger(4), new BinInteger(1)),
		"targetPoint" => new BinStruct([
			"x" => new BinInteger(32, -2147483648),
			"y" => new BinInteger(32, -2147483648),
			"z" => new BinInteger(32, -2147483648),
		]),
	]),
	83 => new BinStruct([
		"speed" => new BinInteger(3),
	]),
	84 => new BinStruct([
		"delta" => new BinInteger(8, -128),
	]),
	85 => new BinStruct([
		"verb" => new BinBlob(new BinInteger(10)),
		"arguments" => new BinBlob(new BinInteger(10)),
	]),
	86 => new BinStruct([
		"alliance" => new BinInteger(32),
		"control" => new BinInteger(32),
	]),
	87 => new BinStruct([
		"unitTag" => new BinInteger(32),
	]),
	88 => new BinStruct([
		"unitTag" => new BinInteger(32),
		"flags" => new BinInteger(8),
	]),
	89 => new BinStruct([
		"conversationId" => new BinInteger(32, -2147483648),
		"replyId" => new BinInteger(32, -2147483648),
	]),
	90 => new BinStruct([
		"purchaseItemId" => new BinInteger(32, -2147483648),
	]),
	91 => new BinStruct([
		"difficultyLevel" => new BinInteger(32, -2147483648),
	]),
	92 => null,
	93 => new BinChoice(new BinInteger(3), [
		0 => null,
		1 => new BinInteger(1),
		2 => new BinInteger(32),
		3 => new BinInteger(32, -2147483648),
		4 => new BinBlob(new BinInteger(11)),
	]),
	94 => new BinStruct([
		"controlId" => new BinInteger(32, -2147483648),
		"eventType" => new BinInteger(32, -2147483648),
		"eventData" => new BinChoice(new BinInteger(3), [
			0 => null,
			1 => new BinInteger(1),
			2 => new BinInteger(32),
			3 => new BinInteger(32, -2147483648),
			4 => new BinBlob(new BinInteger(11)),
		]),
	]),
	95 => new BinStruct([
		"soundHash" => new BinInteger(32),
		"length" => new BinInteger(32),
	]),
	96 => new BinStruct([
		"soundHash" => new BinArray(
			new BinInteger(8),
			new BinInteger(32)
		),
		"length" => new BinArray(
			new BinInteger(8),
			new BinInteger(32)
		),
	]),
	97 => new BinStruct([
		"syncInfo" => new BinStruct([
			"soundHash" => new BinArray(
				new BinInteger(8),
				new BinInteger(32)
			),
			"length" => new BinArray(
				new BinInteger(8),
				new BinInteger(32)
			),
		]),
	]),
	98 => new BinStruct([
		"sound" => new BinInteger(32),
	]),
	99 => new BinStruct([
		"transmissionId" => new BinInteger(32, -2147483648),
	]),
	100 => new BinStruct([
		"target" => new BinStruct([
			"x" => new BinInteger(32, -2147483648),
			"y" => new BinInteger(32, -2147483648),
		]),
		"distance" => new BinInteger(32, -2147483648),
		"pitch" => new BinInteger(32, -2147483648),
		"yaw" => new BinInteger(32, -2147483648),
	]),
	101 => new BinInteger(1),
	102 => new BinStruct([
		"skipType" => new BinInteger(1),
	]),
	103 => new BinStruct([
		"button" => new BinInteger(32),
		"down" => new BinInteger(1),
		"posXUI" => new BinInteger(32),
		"posYUI" => new BinInteger(32),
		"posXWorld" => new BinInteger(32, -2147483648),
		"posYWorld" => new BinInteger(32, -2147483648),
		"posZWorld" => new BinInteger(32, -2147483648),
	]),
	104 => new BinStruct([
		"soundtrack" => new BinInteger(32),
	]),
	105 => new BinStruct([
		"planetId" => new BinInteger(32, -2147483648),
	]),
	106 => new BinStruct([
		"key" => new BinInteger(8, -128),
		"flags" => new BinInteger(8, -128),
	]),
	107 => new BinStruct([
		"resources" => new BinArray(
			new BinInteger(3),
			new BinInteger(32, -2147483648)
		),
	]),
	108 => new BinStruct([
		"fulfillRequestId" => new BinInteger(32, -2147483648),
	]),
	109 => new BinStruct([
		"cancelRequestId" => new BinInteger(32, -2147483648),
	]),
	110 => new BinStruct([
		"researchItemId" => new BinInteger(32, -2147483648),
	]),
	111 => new BinStruct([
		"laggingPlayerId" => new BinInteger(4),
	]),
	112 => new BinStruct([
		"mercenaryId" => new BinInteger(32, -2147483648),
	]),
	113 => new BinStruct([
		"battleReportId" => new BinInteger(32, -2147483648),
		"difficultyLevel" => new BinInteger(32, -2147483648),
	]),
	114 => new BinStruct([
		"battleReportId" => new BinInteger(32, -2147483648),
	]),
	115 => new BinStruct([
		"decrementMs" => new BinInteger(32),
	]),
	116 => new BinStruct([
		"portraitId" => new BinInteger(32, -2147483648),
	]),
	117 => new BinStruct([
		"functionName" => new BinBlob(new BinInteger(7)),
	]),
	118 => new BinStruct([
		"result" => new BinInteger(32, -2147483648),
	]),
	119 => new BinStruct([
		"gameMenuItemIndex" => new BinInteger(32, -2147483648),
	]),
	120 => new BinStruct([
		"reason" => new BinInteger(8, -128),
	]),
	121 => new BinStruct([
		"purchaseCategoryId" => new BinInteger(32, -2147483648),
	]),
	122 => new BinStruct([
		"button" => new BinInteger(16),
	]),
	123 => new BinStruct([
		"recipient" => new BinInteger(2),
		"string" => new BinBlob(new BinInteger(11)),
	]),
	124 => new BinStruct([
		"recipient" => new BinInteger(2),
		"point" => new BinStruct([
			"x" => new BinInteger(32, -2147483648),
			"y" => new BinInteger(32, -2147483648),
		]),
	]),
	125 => new BinStruct([
		"progress" => new BinInteger(32, -2147483648),
	]),
);