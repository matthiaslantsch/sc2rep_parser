from __future__ import absolute_import, print_function, unicode_literals, division
import json
from s2protocol.versions import build, list_all, latest

"""
This script is intended to dump a list of s2protocol versions that are here.
"""

versions = list_all()
print(json.dumps(versions))
