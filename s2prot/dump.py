from __future__ import absolute_import, print_function, unicode_literals, division
import json
import argparse
from s2protocol.versions import build, list_all, latest

"""
This script is intended to dump the type info from the protocol into a json file
"""

parser = argparse.ArgumentParser(description="Dump the type info from a protocol into a json file.")
parser.add_argument('baseBuild', metavar='baseBuild', type=int, nargs=1, help="BaseBuild to dump the protocol for.")
args = parser.parse_args()

# Import it
protocol = build(args.baseBuild[0])

data = {
	"typeinfos": protocol.typeinfos,
	"replay_header_typeid": protocol.replay_header_typeid,
	"replay_initdata_typeid": protocol.replay_initdata_typeid,
	"game_details_typeid": protocol.game_details_typeid,
	"svaruint32_typeid": protocol.svaruint32_typeid,
	"replay_userid_typeid": protocol.replay_userid_typeid,
	"game_eventid_typeid": protocol.game_eventid_typeid,
	"game_event_types": protocol.game_event_types,
	"message_eventid_typeid": protocol.message_eventid_typeid,
	"message_event_types": protocol.message_event_types,
	"tracker_eventid_typeid": protocol.tracker_eventid_typeid,
	"tracker_event_types": protocol.tracker_event_types
}

#f = open('protocol18574.json','w')
#f.write(json.dumps(data, indent=3))
#f.close()
print(json.dumps(data))
