php-binary
==========

A PHP library for parsing structured binary streams.


## Usage

Here is an example binary format:

 1. **4 bytes** of text.
 2. **1 byte** unsigned integer.
 3. A repeating field of **8 bytes** of text followed by a **1 byte** unsigned integer, **3** times.


### Writing a Parser Schema

This format can be parsed as follows:

    $schema = new Binary\Schema;
    $schema->initWithSchemaDefinition(json_decode('

        {
           "sometext": {
               "_type": "Text",
               "size": 4
           },
           "somebyte": {
               "_type": "UnsignedInt",
               "size": 1
           },
           "somefields": {
               "_type": "CompoundField",
               "count": 3,
               "_fields": {
                   "footext": {
                       "_type": "Text",
                       "size": 2
                   },
                   "foobyte": {
                       "_type": "UnsignedInt",
                       "size": 1
                   }
               }
           }
        }

    ');

### Parsing a Stream

You can have php-binary parse a generic stream of bytes and output fields as an associative array matching your schema definition.

    $stream = new Binary\Streams\StringStream("FOOODLOLLOMLON");
    $result = $schema->readStream($stream);

The resulting associative array (shown here as JSON for clarity) in `$result` would contain:

    {
        "sometext": "FOOO",
        "somebyte": 68,
        "somefields": [
            {
                "footext": "LO",
                "foobyte": 76
            },   
            {
                "footext": "LO",
                "foobyte": 77
            },   
            {
                "footext": "LO",
                "foobyte": 78
            }
        ]
    } 
